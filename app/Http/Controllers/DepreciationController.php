<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Site;
use App\Models\Asset;
use App\Models\Module;
use App\Models\Company;
use App\Models\InvHist;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\DepreciationDetail;
use App\Models\DepreciationMaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Contracts\DataTable;
use App\Http\Controllers\Journal\JournalController;

class DepreciationController extends Controller
{
  public function __construct() {
    $this->middleware(['permission']);
    $this->middleware(['permission:create'])->only(['create', 'store']);
  }

  public function index(Request $request)
  {
    if ($request->ajax()) {
      $model = DepreciationMaster::select('id', 'dep_company', 'dep_doc_ref', 'dep_periode', 'dep_eff_date', 'dep_status')->with('detail', 'company');

      return DataTables::of($model)
                      ->editColumn('dep_company', function(DepreciationMaster $dep) {
                        return optional($dep->company)->co_name;
                      })
                      ->orderColumn('dep_status', function ($query, $order) {
                        $query->orderByRaw("
                          CASE
                            WHEN dep_status = 'OPEN' THEN 0
                            WHEN dep_status = 'DONE' THEN 1
                            ELSE 2
                          END $order
                        ");
                      })
                      ->toJson();

      }
    $modules = Module::isSuper()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
    $count = DepreciationMaster::count();
    $menuId = $request->attributes->get('menuId');

    return view('transaction.depreciation.list', compact('modules', 'count', 'menuId'));
  }

  public function create()
  {
    $company = Company::select('co_company', 'co_name', 'last_dep')->active()->get();

    return view('transaction.depreciation.calculate', compact('company'));
  }

  public function getCompany(Request $request)
  {
    $company = Company::select('co_company', 'co_name', 'last_dep')
              ->active()
              ->find($request->company);

    return response()->json($company);
  }

  public function calculate(Request $request)
  {
    $request->validate([
      'cal_company' => 'required',
      // 'cal_dep_transdate' => 'required'
      'cal_date' => 'required',
    ]);


    try {
      //code...
      DB::beginTransaction();

      $company = Company::with('sites')->find($request->cal_company);

      // jika last_dep >= hari ini
      if ($company->last_dep >= Carbon::now()->endOfMonth()->format('Y-m-d')) {
        session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => $company->co_name. ' sudah melakukan depresiasi.'));
        return redirect()->back();
      }
      
      $asset = Asset::with('history', 'category')
                    ->whereIn('inv_company', [$request->cal_company])
                    ->whereIn('inv_status', ['ONHAND', 'RSV'])
                    ->where('inv_obtaindate', '<=', Carbon::parse($request->cal_date)->endofMonth()->format('Y-m-d'))
                    ->where(function ($query) use ($request) {
                      $query->where('inv_last_periode', '<', Carbon::parse($request->cal_date)->endofMonth()->format('Y-m-d'))
                            ->orWhereNull('inv_last_periode');
                    })
                    ->orderBy('inv_last_periode', 'desc')
                    ->get();

      $date = Carbon::parse($request->cal_date)->startofMonth();
      $transdate = Carbon::parse($request->cal_date);
      // DEP/GJB/24/B/00001
      $num = newGetLastDocumentNumber(DepreciationMaster::class, 'dep_doc_ref', array('dep_company' => $request->cal_company), $transdate, 'year', 5, 14, 'dep_eff_date', 'dep_doc_ref');
      $docRef =  'DEP/' . substr($request->cal_company, 0, 3) . '/' . $date->format('y') . '/' . month2roman($date->format('m')) . '/' . str_pad($num, 5, '0', STR_PAD_LEFT);

      $depreMstr = DepreciationMaster::create([
        'dep_company' => $request->cal_company,
        'dep_doc_ref' => $docRef,
        'dep_periode' => $date->format('M-y'),
        'dep_eff_date' => Carbon::parse($request->cal_date)->endofMonth()->toDateString(),
        'dep_status' => 'OPEN',
      ]);

      foreach ($asset as $key => $item) {
        if (is_null($item->inv_last_periode)) {
          # code...
          if ($item->inv_dep_amount >= $item->inv_dep_periode) {
            # code...
            session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Jumlah depresiasi sudah melebihi periode depresiasi.'));
            return redirect()->back();
            // throw new Exception("Jumlah depresiasi sudah melebihi periode depresiasi.");
          }

          if ($item->inv_obtaindate->format('M-y') == $request->cal_date) {
            # update accumulation depreciation
            $accumulateDep = ($item->inv_accumulate_dep + $item->inv_nominal_dep);
            $item->update([
              'inv_accumulate_dep' => $accumulateDep, // accumulate depreciation
              'inv_dep_amount' => $item->inv_dep_amount+1,
            ]);

            # update current price
            $currentPrice = ($item->inv_price - $item->inv_accumulate_dep);
            $item->update([
              'inv_current_price' => $currentPrice, // price after deoreciation
              'inv_last_periode' => Carbon::parse($request->cal_date)->endofMonth()->format('Y-m-d'),
            ]);

            #create inv history
            $invhist = InvHist::create([
              'invhist_transno' => $item->inv_transno,
              'invhist_inv' => $item->id,
              'invhist_category' => $item->inv_category,
              'invhist_site' => $item->inv_site,
              'invhist_loc' => $item->inv_loc,
              'invhist_depreciation' => $item->inv_depreciation,
              'invhist_name' => $item->inv_name,
              'invhist_pic' => $item->inv_pic,
              'invhist_obtaindate' => $item->inv_obtaindate,
              'invhist_price' => $item->inv_price,
              'invhist_status' => $item->inv_status,
              'invhist_desc' => $item->inv_desc,
              'invhist_sn' => $item->inv_sn,
              'invhist_doc_ref' => $item->inv_doc_ref,
              'invhist_merk' => $item->inv_merk,
              'invhist_cur_price' => $item->inv_current_price,
              'invhist_dep_periode' => $item->inv_dep_periode,
              'invhist_dep_amount' => $item->inv_dep_amount,
              'invhist_tag' => $item->inv_tag,
              'invhist_name_short' => $item->inv_name_short,
              'invhist_company' => $item->inv_company,
              'is_vehicle'=> $item->is_vehicle,
            ]);

            # create depreciation detail 
            $depreDtl = DepreciationDetail::create([
              'depdtl_doc_id' => $depreMstr->id,
              'depdtl_asset_transno' => $item->inv_transno,
              'depdtl_company' => $item->inv_company,
              'depdtl_site' => $item->inv_site,
              'depdtl_category' => $item->inv_category,
              'depdtl_acc_accumulate_dep' => $item->category->cat_accumulate_depreciation,
              'depdtl_acc_expense_dep' => $item->category->cat_depreciation_expense,
              'depdtl_asset_price' => $item->inv_price,
              'depdtl_nominal_dep' => $item->inv_nominal_dep,
              'depdtl_accumulate_dep' => $item->inv_accumulate_dep,
              'depdtl_current_price' => $item->inv_current_price,
              'depdtl_dep_amount' => $item->inv_dep_amount,
              'depdtl_desc' => $item->inv_desc,
              'depdtl_doc_ref' => $item->inv_doc_ref,
            ]);

            $company->update([
              'last_dep' => Carbon::parse($request->cal_date)->endofMonth()->format('Y-m-d'),
            ]);

          } else {
            session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Terdapat asset yang belum di depresiasi di bulan '. Carbon::parse($item->inv_obtaindate)->format('M-y') .'.'));
            return redirect()->back();
          }
        } else {
          if ($item->inv_last_periode->format('M-y') == $request->cal_date) {
            session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => $company->co_name. ' sudah melakukan depresiasi.'));
            return redirect()->back();
          } 
          // update data if cal_date + 1mounth dari inv_last_periode
          if (Carbon::parse($item->inv_last_periode)->startofMonth()->addMonth(1)->endofMonth()->format('M-y') != Carbon::parse($request->cal_date)->endofMonth()->format('M-y')) {
            session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Terdapat asset yang belum di depresiasi di bulan '. Carbon::parse($item->inv_last_periode)->startofMonth()->addMonth(1)->endofMonth()->format('M-y') .'.'));
            return redirect()->back();
            // throw new Exception("Depresiasi pada bulan sebelumnya belum dilakukan");
          } else {
            # code...
            if ($item->inv_dep_amount >= $item->inv_dep_periode) {
              # code...
              session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Jumlah depresiasi sudah melebihi periode depresiasi.'));
              return redirect()->back();
              // throw new Exception("Jumlah depresiasi sudah melebihi periode depresiasi.");
            }

            # update accumulation depreciation
            $accumulateDep = ($item->inv_accumulate_dep + $item->inv_nominal_dep);
            $item->update([
              'inv_accumulate_dep' => $accumulateDep, // accumulate depreciation
              'inv_dep_amount' => $item->inv_dep_amount+1,
            ]);

            # update current price
            $currentPrice = ($item->inv_price - $item->inv_accumulate_dep);
            $item->update([
              'inv_current_price' => $currentPrice, // price after deoreciation
              'inv_last_periode' => Carbon::parse($request->cal_date)->endofMonth()->format('Y-m-d'),
            ]);

            #create inv history
            $invhist = InvHist::create([
              'invhist_transno' => $item->inv_transno,
              'invhist_inv' => $item->id,
              'invhist_category' => $item->inv_category,
              'invhist_site' => $item->inv_site,
              'invhist_loc' => $item->inv_loc,
              'invhist_depreciation' => $item->inv_depreciation,
              'invhist_name' => $item->inv_name,
              'invhist_pic' => $item->inv_pic,
              'invhist_obtaindate' => $item->inv_obtaindate,
              'invhist_price' => $item->inv_price,
              'invhist_status' => $item->inv_status,
              'invhist_desc' => $item->inv_desc,
              'invhist_sn' => $item->inv_sn,
              'invhist_doc_ref' => $item->inv_doc_ref,
              'invhist_merk' => $item->inv_merk,
              'invhist_cur_price' => $item->inv_current_price,
              'invhist_dep_periode' => $item->inv_dep_periode,
              'invhist_dep_amount' => $item->inv_dep_amount,
              'invhist_tag' => $item->inv_tag,
              'invhist_name_short' => $item->inv_name_short,
              'invhist_company' => $item->inv_company,
              'is_vehicle'=> $item->is_vehicle,
            ]);

            # create depreciation detail 
            $depreDtl = DepreciationDetail::create([
              'depdtl_doc_id' => $depreMstr->id,
              'depdtl_asset_transno' => $item->inv_transno,
              'depdtl_company' => $item->inv_company,
              'depdtl_site' => $item->inv_site,
              'depdtl_category' => $item->inv_category,
              'depdtl_acc_accumulate_dep' => $item->category->cat_accumulate_depreciation,
              'depdtl_acc_expense_dep' => $item->category->cat_depreciation_expense,
              'depdtl_asset_price' => $item->inv_price,
              'depdtl_nominal_dep' => $item->inv_nominal_dep,
              'depdtl_accumulate_dep' => $item->inv_accumulate_dep,
              'depdtl_current_price' => $item->inv_current_price,
              'depdtl_dep_amount' => $item->inv_dep_amount,
              'depdtl_desc' => $item->inv_desc,
              'depdtl_doc_ref' => $item->inv_doc_ref,
            ]);

            $company->update([
              'last_dep' => Carbon::parse($request->cal_date)->endofMonth()->format('Y-m-d'),
            ]);
          }
        }
      }

      DB::commit();
    } catch (Throwable $th) {
      //throw $th;
      // dd($th);
      DB::rollback();
      return redirect()->back()->with('error', 'Terjadi kesalahan, harap coba beberapa saat.');
    }

    $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Depresiasi berhasil dikalkulasi.'));

    return redirect()->route('depre.index');
  }

  public function createJournal($id)
  {
    # get data from depreciation mstr
    $depreMstr = DepreciationMaster::with('detail')->find($id);
    # get data from depreciation dtl
    $depreDtl = DepreciationDetail::with('depre_mstr')->where('depdtl_doc_id', $id)->get();
    # query grouping by site and category
    $depreGroup = DepreciationDetail::select('depdtl_site', 'depdtl_category', DB::raw('sum(depdtl_nominal_dep) as nominal_dep'), DB::raw('count(depdtl_site) as jumlah_site'), DB::raw('count(depdtl_category) as category'), 'depdtl_acc_accumulate_dep', 'depdtl_acc_expense_dep')
                  ->whereIn('depdtl_doc_id', [$depreMstr->id])
                  ->groupBy('depdtl_site', 'depdtl_category', 'depdtl_acc_accumulate_dep', 'depdtl_acc_expense_dep')
                  ->get();

    $getSite = Site::where('si_company', $depreMstr->dep_company)->where('si_site', 'LIKE', 'H%')->first();
    $category = Category::select('cat_code', 'cat_name', 'cat_depreciation')->get();

    $date = Carbon::parse($depreMstr->dep_periode);
    
    $depreMstr->update([
      'dep_status' => 'DONE',
    ]);

    # payload journal
      $detail = [];
      foreach ($depreGroup as $item) {
        if ($item->depdtl_category != 1) {
          # detail
          array_push(
            $detail,
            array(
              'jld_type' => 'DEBIT',
              'jld_periode' => $date->format('Ym'),
              'jld_site' => $item->depdtl_site,
              'jld_category' => $item->depdtl_category,
              'jld_account' => $item->depdtl_acc_expense_dep,
              'jld_amount' => $item->nominal_dep,
              'jld_cc' => '',
              'jld_rmks' => $item->depdtl_desc != null ? $item->depdtl_desc : '', 
            ),
            array(
              'jld_type' => 'CREDIT',
              'jld_periode' => $date->format('Ym'),
              'jld_site' => $item->depdtl_site,
              'jld_category' => $item->depdtl_category,
              'jld_account' => $item->depdtl_acc_accumulate_dep,
              'jld_amount' => $item->nominal_dep*-1,
              'jld_cc' => '',
              'jld_rmks' => $item->depdtl_desc != null ? $item->depdtl_desc : '', 
            ),
          );
        }
      }

      foreach ($detail as $value) {
        if ($value['jld_category'] != 1) {
          # header
          $payload['jl_period'] = $date->format('Ym');
          $payload['jl_eff_date'] = $date->endofMonth()->format('Y-m-d');
          $payload['jl_hcompany'] = 'H001';
          $payload['jl_company'] = $depreMstr->dep_company;
          $payload['jl_site'] = $getSite->si_site;
          $payload['jl_ref'] = $depreMstr->dep_doc_ref;
          // $payload['jl_ref'] = $item->depdtl_doc_ref;
          $payload['rmks'] = 'DEPRESIASI ASSET';
          $payload['jl_rowttl'] = count($detail);
          $payload['user'] = Auth::user()->usr_nik;
          $payload['detail'] = $detail;
  
          // $payloadJournal = new JournalController();
          // $payloadJournal->journalAsset($payload);
        }
      }

      session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Journal Depresiasi bulan '. $depreMstr->dep_periode->format('M-y') .' berhasil dibuat.'));
      return redirect()->route('depre.index');
  }
}

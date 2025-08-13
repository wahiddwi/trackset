<?php

namespace App\Http\Controllers\Transaction;

use Throwable;
use Carbon\Carbon;
use App\Models\Site;
use App\Models\Asset;
use App\Models\Module;
use App\Models\InvHist;
use App\Models\Category;
use App\Models\Disposal;
use App\Models\GeneralParam;
use Illuminate\Http\Request;
use App\Models\DisposalDetail;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Http\Controllers\Journal\JournalController;

class DisposalController extends Controller
{
  public function __construct() {
    $this->middleware(['permission']);
    $this->middleware(['permission:create'])->only(['create', 'store']);
    $this->middleware(['permission:update'])->only(['edit', 'update']);
  }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $selected_company = Session::get('selected_site')->si_company;
      if ($request->ajax()) {
        $model = Disposal::select('id', 'dis_transno', 'dis_transdate', 'dis_desc', 'dis_status')
                          ->with('detail', 'company', 'site')
                          ->where('dis_company', $selected_company);
        
        return DataTables::of($model)
                          ->orderColumn('dis_status', function ($query, $order) {
                            $query->orderByRaw("
                              CASE 
                                WHEN dis_status = 'RSV' THEN 0
                                WHEN dis_status = 'DISPOSAL' THEN 2
                                WHEN dis_status = 'CANCEL' THEN 1
                                ELSE 3
                              END $order
                            ");
                          })
                          ->orderColumn('dis_transdate', function ($query, $order) {
                            $query->orderBy('dis_transdate', $order);
                          })
                          ->toJson(); 
      }

      $modules = Module::where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
      $menuId = $request->attributes->get('menuId');
      $company = Site::select('si_site', 'si_name', 'si_company')->with('company')->where('si_site', $selected_company)->active()->first();
      $count = Disposal::where('dis_company', $selected_company)->count();


        return view('transaction.disposal.list', compact('modules', 'menuId', 'company', 'count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $selected_company = Session::get('selected_site')->si_company;
      $asset = Asset::select('id', 'inv_transno', 'inv_name', 'inv_status')
              ->where('inv_status', 'ONHAND')
              ->whereIn('inv_company', [$selected_company])
              ->whereNotIn('inv_status', ['SELL', 'DISPOSAL'])
              ->get();

        return view('transaction.disposal.create', compact('selected_company', 'asset'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $request->validate([
        'dis_transdate' => 'required',
        'dis_desc' => 'required',
        'dis_company' => 'required',
        'disdtl_asset_transno.*' => 'required',
        'disdtl_asset_name.*' => 'required',
        'disdtl_desc.*' => 'nullable|max:255',
      ]);


      try {
        //code...
        DB::beginTransaction();
        $date = Carbon::parse($request->dis_transdate);
        $num = newGetLastDocumentNumber(Disposal::class, 'dis_transno', array('dis_company' => $request->dis_company), $date, 'year', 5, 14, 'dis_transdate', 'dis_transno');
        // DIS/GJB/24/B/00001
        $dis_transno = 'DIS/'. substr($request->dis_company, 0, 3) .'/'. $date->format('y') . '/' . month2roman($date->format('m')) . '/' . str_pad($num, 5, '0', STR_PAD_LEFT);
        $line_count = count($request->disdtl_asset_transno);

        $disposal = Disposal::create([
          'dis_transno' => $dis_transno,
          'dis_transdate' => $request->dis_transdate,
          'dis_status' => 'RSV', // RSV, DISPOSAL, CANCEL
          'dis_company' => $request->dis_company,
          // 'dis_site' => $request->dis_site,
          'dis_desc' => $request->dis_desc,
          'created_name' => Auth::user()->usr_name,
        ]);

        for ($i=0; $i < $line_count ; $i++) { 
           # code...
           DisposalDetail::create([
              'disdtl_dis_id' => $disposal->id,
              'disdtl_transdate' => $disposal->dis_transdate,
              'disdtl_asset_transno' => $request->disdtl_asset_transno[$i],
              'disdtl_asset_name' => $request->disdtl_asset_name[$i],
              'disdtl_asset_site' => $request->disdtl_asset_site[$i],
              'disdtl_order' => $request->disdtl_order[$i],
              'disdtl_status' => $disposal->dis_status,
              'disdtl_desc' => $request->disdtl_desc[$i],
              'created_name' => Auth::user()->usr_name,
           ]);
        }
        DB::commit();
      } catch (\Throwable $th) {
        //throw $th;
        DB::rollback();
        $request->session()->flash('notification', array('type' => 'error','title' => 'Gagal!', 'msg' => 'Terjadi kesalahan, harap coba kembali.'));
        return redirect()->back();
      }

      $request->session()->flash('notification', array('type' => 'success','title' => 'Berhasil', 'msg' => 'Barang Berhasil di disposal!'));
      return redirect()->route('disposal.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $disposal = Disposal::with('detail.site', 'company', 'site')->find($id);
      if (is_null($disposal)) {
        return response(array('res' => false));
      }

      return response()->json([
        'res' => true,
        'result' => $disposal
      ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $disposal = Disposal::with('detail', 'company', 'site')->find($id);
      $selected_company = Session::get('selected_site')->si_company;
      $asset = Asset::select('id', 'inv_transno', 'inv_name', 'inv_status')
                      ->whereIn('inv_company', [$selected_company])
                      ->where('inv_status', 'ONHAND')
                      ->get();

        return view('transaction.disposal.edit', compact('disposal', 'asset', 'selected_company'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $request->validate([
        'dis_transdate' => 'required',
        'dis_desc' => 'required',
        'disdtl_asset_transno.*' => 'required',
        'disdtl_asset_site' => 'required',
        'disdtl_asset_name.*' => 'required',
        'disdtl_desc.*' => 'nullable|max:255',
      ]);


      try {
        //code...
        DB::beginTransaction();
        
        $disposal = Disposal::with('detail')->find($id);

        $disposal->update([
          'dis_transdate' => $request->dis_transdate,
          'dis_desc' => $request->dis_desc,
          'dis_status' => $disposal->dis_status,
        ]);

        $disposal->detail->each->delete();

        for ($i=0; $i < count($request->disdtl_asset_transno) ; $i++) {
          # update disposal detail
          DisposalDetail::create([
            'disdtl_dis_id' => $disposal->id,
            'disdtl_transdate' => $disposal->dis_transdate,
            'disdtl_asset_transno' => $request->disdtl_asset_transno[$i],
            'disdtl_asset_name' => $request->disdtl_asset_name[$i],
            'disdtl_asset_site' => $request->disdtl_asset_site[$i],
            'disdtl_desc' => $request->disdtl_desc[$i],
            'disdtl_order' => $request->disdtl_order[$i],
            'disdtl_status' => $disposal->dis_status,
            'updated_name' => Auth::user()->usr_name,
          ]);

        }

        DB::commit();
      } catch (\Throwable $th) {
        //throw $th;
        Log::info('Error Update Disposal : ', $th);
        DB::rollback();
        $request->session()->flash('notification', array('type' => 'error','title' => 'Gagal!', 'msg' => 'Terjadi kesalahan, harap coba kembali.'));
        return redirect()->back();
      }

      $request->session()->flash('notification', array('type' => 'success','title' => 'Berhasil', 'msg' => 'Disposal Berhasil diubah!'));
      return redirect()->route('disposal.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function accept($id)
    {
      try {
        //code...
        DB::beginTransaction();

        $disposal = Disposal::with('detail')->find($id);
        // update disposal_mstr
        $disposal->update([
          'dis_status' => 'DISPOSAL', // RSV, DISPOSAL, CANCEL
          'approved_by' => Auth::user()->usr_nik,
          'approved_name' => Auth::user()->usr_name,
        ]);

        // update disposal detail
        for ($i=0; $i < count($disposal->detail) ; $i++) {
          $disposal->detail[$i]->update([
            'disdtl_status' => $disposal->dis_status,
            'approved_name' => Auth::user()->usr_name,
            'approved_by' => Auth::user()->usr_nik,
          ]);

          // update inv_mstr
          $asset = Asset::where('inv_transno', $disposal->detail[$i]->disdtl_asset_transno)->first();
          $asset->update([
            'inv_status' => $disposal->dis_status,
            'inv_current_price' => 0,
          ]);

          // create inv history
          InvHist::create([
            'invhist_transno' => $disposal->detail[$i]->disdtl_asset_transno,
            'invhist_inv' => $asset->id,
            'invhist_category' => $asset->inv_category,
            'invhist_site' => $asset->inv_site,
            'invhist_loc' => $asset->inv_loc,
            'invhist_depreciation' => $asset->inv_depreciation,
            'invhist_name' => $asset->inv_name,
            'invhist_pic' => $asset->inv_pic,
            'invhist_obtaindate' => $asset->inv_obtaindate,
            'invhist_price' => $asset->inv_price,
            'invhist_status' => $asset->inv_status,
            'invhist_desc' => $asset->inv_desc,
            'invhist_sn' => $asset->inv_sn,
            'invhist_doc_ref' => $asset->inv_doc_ref,
            'invhist_cur_price' => $asset->inv_current_price,
            'invhist_dep_periode' => $asset->inv_dep_periode,
            'invhist_dep_amount' => $asset->inv_dep_amount,
            'invhist_company' => $asset->inv_company,
            'invhist_tag' => $asset->inv_tag,
            'invhist_merk' => $asset->inv_merk,
            'invhist_name_short' => $asset->inv_name_short,
            'is_vehicle' => $asset->is_vehicle,
          ]);

          // journal
          $date = Carbon::parse($disposal->dis_transdate);
          $category = Category::find($asset->inv_category);
          $params = GeneralParam::select('param_sales_profit', 'param_sales_loss', 'param_expense_loss', 
                                          'param_asset_transaction', 'param_cash')->first();
          $getSite = Site::where('si_company', $disposal->dis_company)->where('si_site', 'LIKE', 'H%')->first();

          // detail
          $detail = array();
          if ($category->cat_depreciation != 1) {
            array_push(
              $detail,
              array(
                'jld_type' => 'DEBIT',
                'jld_periode' => $date->format('Ym'),
                'jld_site' => $asset->inv_site,
                'jld_account' => $asset->category->cat_accumulate_depreciation, // AKun AKumulasi Penyusutan
                'jld_amount' => $asset->inv_accumulate_dep,
                'jld_cc' => '',
                'jld_rmks' => $asset->inv_desc != null ? $asset->inv_desc : '',
              ),
              array(
                'jld_type' => 'DEBIT',
                'jld_periode' => $date->format('Ym'),
                'jld_site' => $asset->inv_site,
                'jld_account' => $params->param_expense_loss, // Akun beban Lain-lain
                'jld_amount' => $asset->inv_current_price,
                'jld_cc' => '',
                'jld_rmks' => $asset->inv_desc != null ? $asset->inv_desc : '',
              ),
              array(
                'jld_type' => 'KREDIT',
                'jld_periode' => $date->format('Ym'),
                'jld_site' => $asset->inv_site,
                'jld_account' => $category->cat_asset, // Akun asset tetap
                'jld_amount' => $asset->inv_price,
                'jld_cc' => '',
                'jld_rmks' => $asset->inv_desc != null ? $asset->inv_desc : '',
              )
            );
          }
        }

        // header
        $payload['jl_period'] = $date->format('Ym');
        $payload['jl_eff_date'] = $date->format('Y-m-d');
        $payload['jl_hcompany'] = 'H001';
        $payload['jl_company'] = $disposal->dis_company;
        $payload['jl_site'] = $getSite->si_site;
        $payload['jl_ref'] = $disposal->dis_transno;
        $payload['rmks'] = 'DISPOSAL ASSET';
        $payload['jl_rowttl'] = count($detail);
        $payload['user'] = Auth::user()->usr_nik;
        $payload['detail'] = $detail;

        Log::info('Payload Journal Disposal : ', $payload);
        
        // $payloadJournal = new JournalController();
        // $payloadJournal->journalAsset($payload);

        DB::commit();
      } catch (Throwable $th) {
        //throw $th;
        Log::info('Error POST DISPOSAL : ', $payload);
        DB::rollback();
        session()->flash('notification', array('type' => 'error','title' => 'Gagal!', 'msg' => 'Terjadi kesalahan, harap coba kembali.'));
        return redirect()->back();
      }
      session()->flash('notification', array('type' => 'success','title' => 'Berhasil', 'msg' => 'Disposal Berhasil diubah!'));
      return redirect()->route('disposal.index');
    }

    public function search(Request $request)
    {
      $search = Asset::with('site.company', 'history')
                      ->whereIn('inv_transno', [$request->search])
                      ->whereIn('inv_company', [$request->company])
                      ->where('inv_status', 'ONHAND')
                      ->first();

      if ($search == null) {
        session()->flash('notification', array('type' => 'error','title' => 'Gagal!', 'msg' => 'No. Asset tidak ditemukan!'));
        return redirect()->back();
      }

      return response()->json([
        'res' => true,
        'result' => $search
      ]);
    }

    public function toggleState($id)
    {
      $dis = Disposal::with('detail')->where('dis_status', 'RSV')->find($id);
      $dis->dis_status = 'CANCEL'; // RDV, DISPOSAL, CANCEL
      $dis->save();

      for ($i=0; $i < count($dis->detail) ; $i++) { 
        # code...
        // create disposal detail
        DisposalDetail::create([
          'disdtl_dis_id' => $dis->id,
          'disdtl_transdate' => $dis->dis_transdate,
          'disdtl_asset_transno' => $dis->detail[$i]->disdtl_asset_transno,
          'disdtl_asset_site' => $dis->detail[$i]->disdtl_asset_site,
          'disdtl_asset_name' => $dis->detail[$i]->disdtl_asset_name,
          'disdtl_desc' => $dis->detail[$i]->disdtl_desc,
          'disdtl_order' => $dis->detail[$i]->disdtl_order,
          'disdtl_status' => $dis->dis_status,
        ]);

        // update asset status
        $asset = Asset::whereIn('inv_transno', [$dis->detail[$i]->disdtl_asset_transno])->first();

        $asset->inv_status = 'ONHAND';
        $asset->save();

        // create history
        InvHist::create([
          'invhist_transno' => $dis->detail[$i]->disdtl_asset_transno,
          'invhist_inv' => $asset->id,
          'invhist_category' => $asset->inv_category,
          'invhist_site' => $asset->inv_site,
          'invhist_loc' => $asset->inv_loc,
          'invhist_depreciation' => $asset->inv_depreciation,
          'invhist_name' => $asset->inv_name,
          'invhist_pic' => $asset->inv_pic,
          'invhist_obtaindate' => $asset->inv_obtaindate,
          'invhist_price' => $asset->inv_price,
          'invhist_status' => $asset->inv_status,
          'invhist_desc' => $asset->inv_desc,
          'invhist_sn' => $asset->inv_sn,
          'invhist_doc_ref' => $asset->inv_doc_ref,
          'invhist_cur_price' => $asset->inv_current_price,
          'invhist_dep_periode' => $asset->inv_dep_periode,
          'invhist_dep_amount' => $asset->inv_dep_amount,
          'invhist_company' => $asset->inv_company,
          'invhist_tag' => $asset->inv_tag,
          'invhist_merk' => $asset->inv_merk,
          'invhist_name_short' => $asset->inv_name_short,
          'is_vehicle' => $asset->is_vehicle,
        ]);
      }
      return array('res' => true);
    }
}

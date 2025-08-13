<?php

namespace App\Http\Controllers\Transaction;

use PDF;
use Throwable;
use Carbon\Carbon;
use App\Models\Pic;
use App\Models\Site;
use App\Models\User;
use App\Models\Asset;
use App\Models\Module;
use App\Models\InvHist;
use App\Models\Category;
use App\Models\Location;
use App\Models\Transfer;
use Illuminate\Http\Request;
use App\Models\TransferDetail;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Journal\JournalController;

class TransferController extends Controller
{
  public function __construct() {
    $this->middleware(['permission']);
    $this->middleware(['permission:create'])->only(['create', 'store']);
    $this->middleware(['permission:update'])->only(['edit', 'update', 'toggleState']);
    $this->middleware(['permission:post'])->only(['accept']);
    $this->middleware(['permission:print'])->only(['print']);
    $this->middleware(['permission:delete'])->only(['delete']);
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
        $model = Transfer::select('id', 'trf_transno', 'trf_site_from', 'trf_loc_from', 'trf_site_to',
                                  'trf_loc_to', 'trf_status', 'trf_transdate')
                          ->with('detail', 'siteFrom', 'locFrom', 'locTo', 'siteTo', 'user')
                          ->where('trf_company', $selected_company)
                          ->where('trf_status', '!=', 'ONHAND');

       return DataTables::of($model)
                ->editColumn('trf_site_from', function (Transfer $transfer) {
                    return optional($transfer->siteFrom)->si_name;
                })
                ->editColumn('trf_loc_from', function (Transfer $transfer) {
                    return optional($transfer->locFrom)->loc_name;
                })
                ->editColumn('trf_site_to', function (Transfer $transfer) {
                    return optional($transfer->siteTo)->si_name;
                })
                ->editColumn('trf_loc_to', function (Transfer $transfer) {
                    return optional($transfer->locTo)->loc_name;
                })
                ->orderColumn('trf_status', function ($query, $order) {
                  $query->orderByRaw("
                    CASE
                      WHEN trf_status = 'DRAFT' THEN 0
                      WHEN trf_status = 'TRF' THEN 1
                      WHEN trf_status = 'CANCEL' THEN 2
                      ELSE 3
                    END $order
                  ");
                })
                ->toJson();
      }

    $modules = Module::isSuper()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
    $count = Transfer::where('trf_company', $selected_company)->count();
    $menuId = $request->attributes->get('menuId');

    session()->forget('asset_site');
    return view('transaction.transfer.list', compact('modules', 'count', 'menuId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $selected_company = Session::get('selected_site')->si_company;
      $sites = Site::select('si_site', 'si_name', 'si_company')
                    ->whereIn('si_company', [$selected_company])
                    ->where('si_active', true)
                    ->get();
      $users = Pic::select('pic_nik', 'pic_name', 'pic_status')->where('pic_status', true)->get();
      $asset = Asset::select('id', 'inv_transno', 'inv_name', 'inv_company', 'inv_site', 
                            'inv_status', 'inv_merk')
                      ->whereIn('inv_company', [$selected_company])
                      ->where('inv_status', 'ONHAND')
                      ->get();
                      
      return view('transaction.transfer.create', compact('sites', 'users', 'asset', 'selected_company'));

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
          'trf_company' => 'required',
          'trf_transdate' => 'required',
          'trf_site_from' => 'required',
          'trf_loc_from' => 'required',
          'trf_pic_type_from' => 'required',
          'trf_pic_from' => 'required',
          'trf_site_to' => 'required',
          'trf_loc_to' => 'required',
          'trf_pic_type_to' => 'required',
          'trf_pic_to' => 'required',
          'trf_desc' => 'nullable|max:255',
          'trfdtl_asset_no.*' => 'required',
          'trfdtl_asset_name.*' => 'required',
          'trfdtl_desc.*' => 'nullable|max:255',
          'trfdtl_order.*' => 'required',
        ]);

        try {
          DB::beginTransaction();
          $date = Carbon::parse($request->trf_transdate);

          $num = newGetLastDocumentNumber(Transfer::class, 'trf_transno', array('trf_company' => $request->trf_company), $date, 'year', 5, 14, 'trf_transdate', 'trf_transno');
          $trf_transno =  'TRF/' . substr($request->trf_company, 0, 3) . '/' . $date->format('y') . '/' . month2roman($date->format('m')) . '/' . str_pad($num, 5, '0', STR_PAD_LEFT);

          $line_count = count($request->trfdtl_order);
          
          $transfer = new Transfer;
          $transfer->trf_transno = $trf_transno;
          $transfer->trf_company = $request->trf_company;
          $transfer->trf_transdate = $request->trf_transdate;
          $transfer->trf_site_from = $request->trf_site_from;
          $transfer->trf_loc_from = $request->trf_loc_from;
          $transfer->trf_pic_type_from = $request->trf_pic_type_from;
          $transfer->trf_pic_from = $request->trf_pic_from;
          $transfer->trf_site_to = $request->trf_site_to;
          $transfer->trf_loc_to = $request->trf_loc_to;
          $transfer->trf_pic_type_to = $request->trf_pic_type_to;
          $transfer->trf_pic_to = $request->trf_pic_to;
          $transfer->trf_desc = $request->trf_desc;
          $transfer->trf_status = 'DRAFT'; // DRAFT, TRF, CANCEL
          $transfer->trf_count = $line_count;
          $transfer->trf_created_name = Auth::user()->usr_name;
          $transfer->save();

          $histories = array();
          for ($i=0; $i < $line_count; $i++) { 
            // create transfer detail
            TransferDetail::create([
              'trfdtl_id' => $transfer->id,
              'trfdtl_transno' => $request->trfdtl_asset_no[$i],
              'trfdtl_name' => $request->trfdtl_asset_name[$i],
              'trfdtl_status' => $transfer->trf_status,
              'trfdtl_desc' => $request->trfdtl_desc[$i],
              'trfdtl_transdate' => $transfer->trf_transdate,
              'trfdtl_company' => $transfer->trf_company,
              'trfdtl_site_from' => $transfer->trf_site_from,
              'trfdtl_loc_from' => $transfer->trf_loc_from,
              'trfdtl_pic_type_from' => $transfer->trf_pic_type_from,
              'trfdtl_pic_from' => $transfer->trf_pic_from,
              'trfdtl_site_to' => $transfer->trf_site_to,
              'trfdtl_loc_to' => $transfer->trf_loc_to,
              'trfdtl_pic_type_to' => $transfer->trf_pic_type_to,
              'trfdtl_pic_to' => $transfer->trf_pic_to,
              'trfdtl_order' => $request->trfdtl_order[$i],
              'trfdtl_created_name' => Auth::user()->usr_name
            ]);
          }

          DB::commit();
        } catch (Throwable $th) {
          dd($th);
          //throw $th;
          DB::rollback();
          $request->session()->flash('notification', array('type' => 'error', 'title' => 'Gagal!', 'msg' => 'Harap coba beberapa saat'));
          return redirect()->back();
        }
        $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Data berhasil disimpan!'));
        session()->forget('asset_site');
        return redirect()->route('transfer.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $transfer = Transfer::
                          with('detail.pic_to', 'detail.site_to', 'siteFrom', 'locFrom', 
                                  'siteTo', 'locTo', 'userFrom', 'userTo')
                          ->find($id);

      return response()->json([
        'res' => true,
        'data' => $transfer,
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
      $trf = Transfer::with('siteFrom', 'locFrom', 'userFrom', 'detail', 'siteTo', 'locTo', 'userTo')->find($id);
      $selected_company = Session::get('selected_site')->si_company;
      $sites = Site::select('si_site', 'si_name', 'si_company')
                    ->whereIn('si_company', [$selected_company])
                    ->where('si_active', true)
                    ->get();
                    
      $users = Pic::select('pic_nik', 'pic_name', 'pic_status')->where('pic_status', true)->get();
      $asset = Asset::select('id', 'inv_transno', 'inv_name', 'inv_company', 'inv_site', 
                            'inv_status', 'inv_merk')
                      ->whereIn('inv_company', [$selected_company])
                      ->where('inv_status', 'ONHAND')
                      ->get();

      return view('transaction.transfer.edit', compact('trf', 'selected_company', 'sites', 'users', 'asset'));
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
        'trf_company' => 'required',
        'trf_transdate' => 'required',
        'trf_site_from' => 'required',
        'trf_loc_from' => 'required',
        'trf_pic_type_from' => 'required',
        'trf_pic_from' => 'required',
        'trf_site_to' => 'required',
        'trf_loc_to' => 'required',
        'trf_pic_type_to' => 'required',
        'trf_pic_to' => 'required',
        'trf_desc' => 'nullable|max:255',
        'trfdtl_asset_no.*' => 'required',
        'trfdtl_asset_name.*' => 'required',
        'trfdtl_desc.*' => 'nullable|max:255',
        'trfdtl_order.*' => 'required',
      ]);

      try {
        //code...
        DB::beginTransaction();
        $trf = Transfer::with('detail')->find($id);
        $line_count = count($request->trfdtl_order);
        // dd($line_count);
        // dd($trf);
        $trf->update([
          'trf_company' => $request->trf_company,
          'trf_transdate' => $request->trf_transdate,
          'trf_site_from' => $request->trf_site_from,
          'trf_loc_from' => $request->trf_loc_from,
          'trf_pic_type_from' => $request->trf_pic_type_from,
          'trf_pic_from' => $request->trf_pic_from,
          'trf_site_to' => $request->trf_site_to,
          'trf_loc_to' => $request->trf_loc_to,
          'trf_pic_type_to' => $request->trf_pic_type_to,
          'trf_pic_to' => $request->trf_pic_to,
          'trf_status' => 'DRAFT', // DRAFT, TRF, CANCEL
          'trf_desc' => $request->trf_desc,
          'trf_count' => $line_count,
          'trf_updated_name' => Auth::user()->usr_name,
        ]);

        $trf->detail->each->delete();

        // $detail = array();
        for ($i=0; $i < $line_count ; $i++) { 
          TransferDetail::create([
            'trfdtl_id' => $trf->id,
            'trfdtl_transno' => $request->trfdtl_asset_no[$i],
            'trfdtl_name' => $request->trfdtl_asset_name[$i],
            'trfdtl_status' => $trf->trf_status,
            'trfdtl_desc' => $request->trfdtl_desc[$i],
            'trfdtl_transdate' => $trf->transdate,
            'trfdtl_company' => $trf->trf_company,
            'trfdtl_site_from' => $trf->trf_site_from,
            'trfdtl_loc_from' => $trf->trf_loc_from,
            'trfdtl_pic_type_from' => $trf->trf_pic_type_from,
            'trfdtl_pic_from' => $trf->trf_pic_from,
            'trfdtl_site_to' => $trf->trf_site_to,
            'trfdtl_loc_to' => $trf->trf_loc_to,
            'trfdtl_pic_type_to' => $trf->trf_pic_type_to,
            'trfdtl_pic_to' => $trf->trf_pic_to,
            'trfdtl_order' => $request->trfdtl_order[$i],
            'trfdtk_updated_name' => Auth::user()->usr_name,
          ]);
        }
        DB::commit();
      } catch (\Throwable $th) {
        //throw $th;
        Log::info('Error Update Transfer', $th);
        DB::rollback();
        $request->session()->flash('notification', array('type' => 'error', 'title' => 'Gagal!', 'msg' => 'Harap coba beberapa saat'));
        return redirect()->back();
      }
      $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Data berhasil diupdate'));
      return redirect()->route('transfer.index');
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

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required',
            'company' => 'required',
        ]);

        $asset = Asset::where('inv_transno', 'LIKE', '%'. $request->search .'%')
                    ->where('inv_company', $request->company)
                    ->where('inv_status', 'ONHAND')
                    ->with('site', 'location', 'pic')
                    ->first();

        return response()->json([
          'res' => true,
          'data' => $asset
        ]);
    }

    public function getLocation(Request $request)
    {
      $locations = Location::where('loc_site', $request->site_id)->get();
      return response()->json($locations);
    }

    public function getType()
    {
      $selected_company = Session::get('selected_site')->si_company;
      $users = Pic::select('pic_nik', 'pic_name', 'pic_status')->where('pic_status', true)->get();
      $sites = Site::select('si_site', 'si_name', 'si_active')->whereIn('si_company', [$selected_company])->where('si_active', true)->get();

        return response()->json([
            'user' => $users,
            'site' => $sites
        ], 200);
    }

    public function print($id)
    {
      $data = Transfer::with('detail', 'siteFrom', 'siteTo', 'locFrom','locTo', 'userFrom', 'userTo')->find($id);

      if (is_null($data)) {
        return 'No Data';
      }

      $print = PDF::loadview('transaction.transfer.print_v2', compact('data'));
      $print->setPaper(config('constant.pdf.paperDotMatrix'));
      return $print->stream();
    }

    public function accept($id)
    {
      $trf = Transfer::with('detail')->find($id);

      // update trf
      $trf->trf_status = 'TRF';
      $trf->trf_approver_name = Auth::user()->usr_name;
      $trf->trf_approver_nik = Auth::user()->usr_nik;
      $trf->save();

      $histories = array();
      foreach ($trf->detail as $key => $dtl) {
        # update trf_detail
        $dtl->update([
          'trfdtl_status' => $trf->trf_status,
          'trfdtl_approver_name' => $trf->trf_approver_name,
          'trfdtl_approver_nik' => $trf->trf_approver_nik
        ]);

        $asset = Asset::whereIn('inv_transno', [$dtl->trfdtl_transno])
                        ->first();

        // update asset
        $asset->update([
          'inv_status' => 'ONHAND',
          'inv_site' => $trf->trf_site_to,
          'inv_loc' => $trf->trf_loc_to,
          'inv_pic_type' => $trf->trf_pic_type_to,
          'inv_pic' => $trf->trf_pic_to,
        ]);
        
        // create history
        InvHist::create([
          'invhist_transno' => $asset->inv_transno,
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

        // journal transfer
        $date = Carbon::parse($trf->trf_transdate);
        $category = Category::find($asset->inv_category);

        $detail = [];
        if ($asset->inv_accumulate_dep == 0) {
          # transfer (belum depresiasi)
          array_push(
            $detail,
            array(
              'jld_type' => 'DEBIT',
              'jld_periode' => $date->format('Ym'),
              'jld_site' => $trf->trf_site_to, // site tujuan
              'jld_account' => $category->cat_asset, // Akun asset
              'jld_amount' => $asset->inv_price, // harga asset
              'jld_cc' => '',
              'jld_rmks' => $asset->inv_desc,
            ),
            array(
              'jld_type' => 'KREDIT',
              'jld_periode' => $date->format('Ym'),
              'jld_site' => $trf->trf_site_from, // site asal
              'jld_account' => $category->cat_asset, // Akun Asset
              'jld_amount' => $asset->inv_price, // Akun Asset
              'jld_cc' => '',
              'jld_rmks' => $asset->inv_desc,
            ),
          );
        } else {
          array_push(
            $detail,
            array(
              'jld_type' => 'DEBIT',
              'jld_periode' => $date->format('Ym'),
              'jld_site' => $trf->trf_site_to, // site tujuan
              'jld_account' => $category->cat_asset, // Akun asset
              'jld_amount' => $asset->inv_price, // harga asset
              'jld_cc' => '',
              'jld_rmks' => $asset->inv_desc,
            ),
            array(
              'jld_type' => 'DEBIT',
              'jld_periode' => $date->format('Ym'),
              'jld_site' => $trf->trf_site_to, // site Tujuan
              'jld_account' => $category->cat_accumulate_depreciation, // Akun AKumulasi Depresiasi
              'jld_amount' => $asset->inv_accumulate_dep, // Akumulasi Depresiasi
              'jld_cc' => '',
              'jld_rmks' => $asset->inv_desc,
            ),
            array(
              'jld_type' => 'KREDIT',
              'jld_periode' => $date->format('Ym'),
              'jld_site' => $trf->trf_site_from, // site Asal
              'jld_account' => $category->cat_asset, // Akun asset
              'jld_amount' => $asset->inv_price, // harga asset
              'jld_cc' => '',
              'jld_rmks' => $asset->inv_desc,
            ),
            array(
              'jld_type' => 'KREDIT',
              'jld_periode' => $date->format('Ym'),
              'jld_site' => $trf->trf_site_to, // site asal
              'jld_account' => $category->cat_asset, // Akun Asset
              'jld_amount' => $asset->inv_accumulate_dep, // Akumulasi Depresiasi
              'jld_cc' => '',
              'jld_rmks' => $asset->inv_desc,
            ),
          );
        }

        if ($detail) {
          # header
          $payload['jl_period'] = $date->format('Ym');
          $payload['jl_eff_date'] = $date->format('Ym');
          $payload['hcompany'] = 'H001';
          $payload['jl_company'] = $asset->inv_company;
          $payload['jl_site'] = $asset->inv_site;
          $payload['jl_ref'] = $asset->inv_doc_ref;
          $payload['rmks'] = 'Transfer Asset';
          $payload['jl_rowttl'] = count($detail);
          $payload['user'] = Auth::user()->usr_nik;
          $payload['detail'] = $detail;

          Log::info('Payload Journal : ', $payload);

          // $payloadJournal = new JournalController();
          // $payloadJournal->journalAsset($ayload);
        }
      }

      return array('res' => true);
    }

    public function getAssetBySite(Request $request)
    {
      $asset = Asset::whereIn('inv_site', [$request->site_from])
                    ->where('inv_status', 'ONHAND')
                    ->get();

      return response()->json(array('res' => true, 'data' => $asset));
    }

    public function resetSelectSite(Request $request)
    {
      $asset = Asset::select('id', 'inv_transno', 'inv_name', 'inv_company', 'inv_site', 
                            'inv_status', 'inv_merk')
                      ->whereIn('inv_company', [$request->company])
                      ->where('inv_status', 'ONHAND')
                      ->get();

      return response()->json(array('res' => true, 'data' => $asset));
    }
}

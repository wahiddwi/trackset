<?php

namespace App\Http\Controllers;

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
use App\Models\SiteUser;
use App\Models\Transfer;
use Illuminate\Http\Request;
use App\Models\TransferDetail;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Journal\JournalController;

class TransferController extends Controller
{
    public function __construct() {
        $this->middleware(['permission']);
        $this->middleware(['permission:create'])->only(['create', 'store']);
        $this->middleware(['permission:update'])->only(['edit', 'update']);
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
        if ($request->ajax()) {

            $model = Transfer::with('siteFrom', 'locFrom', 'locTo', 'siteTo', 'user', 'detail')->isSuper();

            return DataTables::of($model)
                    ->editColumn('transfer.site_from', function (Transfer $transfer) {
                        // dd($transfer->site->si_name);
                        return $transfer->siteFrom->si_name;
                    })
                    ->editColumn('transfer.loc_from', function (Transfer $transfer) {
                        return $transfer->locFrom->loc_name;
                    })
                    ->editColumn('transfer.site_to', function (Transfer $transfer) {
                        // dd($transfer->siteTo);
                        return $transfer->siteTo->si_name;
                    })
                    ->editColumn('transfer.loc_to', function (Transfer $transfer) {
                        return $transfer->locTo->loc_name;
                    })
                    ->editColumn('transfer.pic_to', function (Transfer $transfer) {
                        if ($transfer->user != null && $transfer->trf_pic == $transfer->user->usr_nik) {
                            return $transfer->user->usr_name;
                        } else {
                            return $transfer->siteTo->si_name;
                        }
                    })
                    ->editColumn('transfer.assetno', function (Transfer $transfer) {
                      return $transfer->detail->trf_detail_transno;
                    })
                    ->orderColumn('trf_status', function ($query, $order) {
                      $query->orderByRaw("
                        CASE
                          WHEN trf_status = 'TRF' THEN 0
                          WHEN trf_status = 'ONHAND' THEN 1
                          ELSE 2
                        END $order
                      ");
                    })
                    ->toJson();
        }

        $modules = Module::isSuper()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
        $count = Transfer::isSuper()->count();
        $menuId = $request->attributes->get('menuId');

        return view('transaction.transfer.list', compact('modules', 'count', 'menuId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $avail_site = array_keys(Session::get('available_sites')->toArray());
        $getCompany = Site::select('si_company')->where('si_site', $avail_site)->first();
        $sites = Site::select('si_site', 'si_name', 'si_active', 'si_company')
                      ->where('si_company', $getCompany->si_company)
                      ->where('si_active', true)
                      ->get();
        // $users = User::select('usr_nik', 'usr_name', 'usr_status')->where('usr_status', true)->get();
        $users = Pic::select('pic_nik', 'pic_name', 'pic_status')->where('pic_status', true)->get();
        $asset = Asset::select('id', 'inv_transno', 'inv_name', 'inv_company', 'inv_site', 'inv_status', 'inv_merk')
                  ->whereIn('inv_company', [$getCompany->si_company])
                  ->where('inv_status', 'ONHAND')
                  ->get();
                  

        return view('transaction.transfer.create', compact('sites', 'users', 'getCompany', 'asset'));
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
            'trf_site_from' => 'required',
            'trf_loc_from' => 'required',
            'trf_site_to' => 'required',
            'trf_loc_to' => 'required',
            'pic_type_to' => 'required',
            'trf_pic_to' => 'required',
            'trf_transdate' => 'required',
            'trf_desc' => 'nullable',

            'hidden_detail_transno' => 'required',
            'hidden_detail_name' => 'required',
        ]);
        // dd($request->all());

        try {
            DB::beginTransaction();

            $date = Carbon::parse(Carbon::now()->locale('id-ID'))->format('m/y');
            // $currentDate = $date['month'] . '/' . $date['year'];
            // $invId = IdGenerator::generate(['table' => 'inv_mstr', 'field' => 'inv_transno', 'length' => 17, 'prefix' => 'AST/' . $date . '/', 'reset_on_prefix_change' => true]);
            $trfId = IdGenerator::generate(['table' => 'transfer', 'field' => 'trf_id', 'length' => 16, 'prefix' => 'TRF/' . $date . '/', 'reset_on_prefix_change' => true]);

            // create transfer
            $transfer = new Transfer;
            $transfer->trf_id = $trfId;
            $transfer->trf_site_from = $request->trf_site_from;
            $transfer->trf_loc_from = $request->trf_loc_from;
            $transfer->trf_site_to = $request->trf_site_to;
            $transfer->trf_loc_to = $request->trf_loc_to;
            $transfer->pic_type_from = $request->pic_type_from;
            $transfer->trf_pic_from = $request->trf_pic_from;
            $transfer->pic_type_to = $request->pic_type_to;
            $transfer->trf_pic_to = $request->trf_pic_to;
            $transfer->trf_status = 'ONHAND'; // ONHAND
            $transfer->trf_name = $request->trf_name;
            $transfer->trf_transdate = $request->trf_transdate;
            $transfer->trf_desc = $request->trf_desc;
            $transfer->trf_created_name = Auth::user()->usr_name;
            $transfer->trf_approver_nik = Auth::user()->usr_nik;
            $transfer->trf_approver_name = Auth::user()->usr_name;
            $transfer->save();
            
            // create transfer detail
            TransferDetail::create([
              'trf_id' => $transfer->id,
              'trf_detail_transno' => $request->hidden_detail_transno,
              'trf_detail_name' => $transfer->trf_name,
              'trf_detail_status' => $transfer->trf_status,
              'trf_detail_pic' => $transfer->trf_pic_to,
              'trf_detail_desc' => $request->trf_detail_desc,
              'trfdtl_created_name' => $transfer->trf_created_name,
              'trfdtl_approver_nik' => $transfer->trf_approver_nik,
              'trfdtl_approver_name' => $transfer->trf_approver_name,
            ]);

            $asset = Asset::whereIn('inv_transno', [$request->hidden_detail_transno])
                          // ->where('inv_status', 'ONHAND')
                          ->first();
            // dd($asset);

            // update inv_mstr
            $asset->update([
              'inv_status' => $transfer->trf_status,
              'inv_site' => $transfer->trf_site_to,
              'inv_loc' => $transfer->trf_loc_to,
              'inv_pic_type' => $transfer->pic_type_to,
              'inv_pic' => $transfer->trf_pic_to,
            ]);

            // create inv_hist
            $history['invhist_transno'] = $asset->inv_transno;
            $history['invhist_inv'] = $asset->id;
            $history['invhist_category'] = $asset->inv_category;
            $history['invhist_site'] = $asset->inv_site;
            $history['invhist_loc'] = $asset->inv_loc;
            $history['invhist_depreciation'] = $asset->inv_depreciation;
            $history['invhist_name'] = $asset->inv_name;
            $history['invhist_pic'] = $asset->inv_pic;
            $history['invhist_obtaindate'] = $asset->inv_obtaindate;
            $history['invhist_price'] = $asset->inv_price;
            $history['invhist_status'] = $transfer->trf_status;
            $history['invhist_desc'] = $asset->inv_desc;
            $history['invhist_sn'] = $asset->inv_sn;
            $history['invhist_doc_ref'] = $asset->inv_doc_ref;
            $history['invhist_merk'] = $asset->inv_merk;
            $history['invhist_cur_price'] = $asset->inv_current_price;
            $history['invhist_dep_periode'] = $asset->inv_dep_periode;
            $history['invhist_dep_amount'] = $asset->inv_dep_amount;
            $history['invhist_tag'] = $asset->inv_tag;
            $history['invhist_name_short'] = $asset->inv_name_short;
            $history['is_vehicle'] = $asset->is_vehicle;
            
            $invHistory = new InventoryController();
            $invHistory->store($history);

            $date = Carbon::parse($request->trf_transdate);
            $category = Category::find($asset->inv_depreciation);

            // create journal transfer
            if ($asset->inv_depreciation != 1) {
              # category depreciation != non depreciation
              if ($request->trf_site_from != $request->trf_site_to) {
                # site from != site to
                if ($asset->inv_accumulate_dep == 0) {
                  # if inv_accumulate_dep belom pernah ada penyusutan
                  $data = [];
                  array_push(
                    $data,
                    array(
                      'jld_type' => 'DEBIT',
                      'jld_periode' => $date->format('Ym'),
                      'jld_site' => $asset->inv_site,
                      'jld_account' => $category->cat_asset, // Akun Asset Tetap
                      'jld_amount' => $asset->inv_price,
                      'jld_cc' => '',
                      'jld_rmks' => $asset->inv_desc == null ? '' : $asset->inv_desc,
                    ),
                    array(
                      'jld_type' => 'CREDIT',
                      'jld_periode' => $date->format('Ym'),
                      'jld_site' => $asset->inv_site,
                      'jld_account' => $category->cat_asset, // Akun Asset Tetap
                      'jld_amount' => $asset->inv_price*-1,
                      'jld_cc' => '',
                      'jld_rmks' => $asset->inv_desc == null ? '' : $asset->inv_desc,
                    )
                  );
                } else {
                  $data = [];
                  array_push(
                    $data,
                    array(
                      'jld_type' => 'DEBIT',
                      'jld_periode' => $date->format('Ym'),
                      'jld_site' => $asset->inv_site,
                      'jld_account' => $category->cat_asset, // Akun Asset Tetap
                      'jld_amount' => $asset->inv_price,
                      'jld_cc' => '',
                      'jld_rmks' => $asset->inv_desc == null ? '' : $asset->inv_desc,
                    ),
                    array(
                      'jld_type' => 'DEBIT',
                      'jld_periode' => $date->format('Ym'),
                      'jld_site' => $asset->inv_site,
                      'jld_account' => $category->cat_accumulate_depreciation, // Transaksi Aktiva Tetap
                      'jld_amount' => $asset->inv_price,
                      'jld_cc' => '',
                      'jld_rmks' => $asset->inv_desc == null ? '' : $asset->inv_desc,
                    ),
                    array(
                      'jld_type' => 'CREDIT',
                      'jld_periode' => $date->format('Ym'),
                      'jld_site' => $asset->inv_site,
                      'jld_account' => $category->cat_asset, // Akun Asset Tetap
                      'jld_amount' => $asset->inv_price*-1,
                      'jld_cc' => '',
                      'jld_rmks' => $asset->inv_desc == null ? '' : $asset->inv_desc,
                    ),
                    array(
                      'jld_type' => 'CREDIT',
                      'jld_periode' => $date->format('Ym'),
                      'jld_site' => $asset->inv_site,
                      'jld_account' => $category->cat_accumulate_depreciation, // Transaksi Aktiva Tetap
                      'jld_amount' => $asset->inv_price*-1,
                      'jld_cc' => '',
                      'jld_rmks' => $asset->inv_desc == null ? '' : $asset->inv_desc,
                    )
                  );

                }
                  // header
                  $payload['jl_period'] = $date->format('Ym');
                  $payload['jl_eff_date'] = $date->format('Y-m-d');
                  $payload['jl_hcompany'] = 'H001';
                  $payload['jl_company'] = $asset->inv_company;
                  $payload['jl_site'] = $asset->inv_site;
                  $payload['jl_ref'] = $asset->inv_doc_ref;
                  $payload['rmks'] = 'TRANSFER ASSET';
                  $payload['jl_rowttl'] = count($data);
                  $payload['user'] = Auth::user()->usr_nik;
                  $payload['detail'] = $data;

                  $payloadJournal = new JournalController();
                  $payloadJournal->journalAsset($payload);
              }
            }

            DB::commit();
        } catch (Throwable $th) {
            DB::rollback();

            dd($th->getMessage());

            $request->session()->flash('notification', array('type' => 'error', 'title' => 'Gagal!', 'msg' => 'Silahkan coba beberapa saat.'));

            return redirect()->back();
        }

        $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil!', 'msg' => 'Transfer barang berhasil diproses.'));

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
        $transfer = Transfer::with('detail', 'siteFrom', 'locFrom', 'siteTo', 'locTo', 'userFrom', 'userTo')->find($id);
        // dd($transfer);
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
        $avail_site = array_keys(Session::get('available_sites')->toArray());
        $getCompany = Site::select('si_company')->where('si_site', $avail_site)->first();
        $sites = Site::select('si_site', 'si_name', 'si_active', 'si_company')
                      ->where('si_company', $getCompany->si_company)
                      ->where('si_active', true)
                      ->get();
        $transfer = Transfer::with('detail', 'siteFrom', 'siteTo', 'locFrom', 'locTo', 'userFrom', 'userTo')->find($id);
        // $sites = Site::select('si_site', 'si_name', 'si_active')->where('si_active', true)->get();
        $currentSite = SiteUser::select('su_user', 'su_site')->with('user')->whereIn('su_site', $avail_site)->get();
        // $users = User::select('usr_nik', 'usr_name', 'usr_status', 'usr_id')->where('usr_status', true)->get();
        // $user = 
        $users = DB::table('users as u')
                    ->join('site_user as su', 'su.su_user', '=', 'u.usr_id')
                    ->join('sites as si', 'su.su_site', '=', 'si.si_site')
                    ->select('usr_id', 'usr_nik', 'usr_name', 'usr_status', 'si.si_company', 'su.su_default', 'si.si_site', 'si.si_name')
                    ->where('u.usr_status', true)
                    ->where('su.su_default', true)
                    ->groupBy('usr_id', 'usr_nik', 'si.si_company', 'su.su_default', 'si.si_site', 'si.si_name')
                    ->get();

        $locations = Location::select('loc_site', 'loc_name', 'loc_active', 'id')->where('loc_active', true)->get();
        $asset = Asset::select('id', 'inv_transno', 'inv_name', 'inv_company', 'inv_site', 'inv_status', 'inv_merk')
                  ->whereIn('inv_company', [$getCompany->si_company])
                  ->where('inv_status', 'ONHAND')
                  ->get();

        return view('transaction.transfer.edit', compact('transfer', 'sites', 'users', 'locations', 'currentSite', 'getCompany', 'asset', 'sites'));
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
        'trf_site_from' => 'required',
        'trf_site_to' => 'required',
        'trf_loc_to' => 'required',
        'pic_type_to' => 'required',
        'trf_pic_to' => 'required',
        'trf_transdate' => 'required',
        'trf_desc' => 'nullable',
    ]);

    try {
        DB::beginTransaction();

        $transfer = Transfer::with('detail', 'detail.asset')->find($id);

        // TransferDetail::where('trf_id', $id)->delete();

        $req = $request->all();

        // update transfer
        $transfer->update([
            'trf_site_from' => $req['trf_site_from'],
            'trf_loc_from' => $req['trf_loc_from'],
            'pic_type_from' => $req['pic_type_from'],
            'trf_pic_from' => $req['trf_pic_from'],
            'trf_site_to' => $req['trf_site_to'],
            'trf_loc_to' => $req['trf_loc_to'],
            'pic_type_to' => $req['pic_type_to'],
            'trf_pic_to' => $req['trf_pic_to'],
            'trf_name' => $req['trf_name'],
            'trf_transdate' => $req['trf_transdate'],
            'trf_desc' => $req['trf_desc'],
            'trf_updated_name' => Auth::user()->usr_name,
        ]);

        // update transfer detail
        $transfer->detail->update([
          'trf_detail_name' => $req['hidden_detail_name'],
          'trf_detail_pic' => $transfer->trf_pic_to,
          'trf_detail_desc' => $req['trf_detail_desc'],
          'trf_updated_name' => $transfer->trf_updated_name,
        ]);

        DB::commit();

    } catch (\Throwable $th) {
        //throw $th;
        DB::rollback();

        return $th->getMessage();

        $request->session()->flash('notification', array('type' => 'error', 'title' => 'Gagal!', 'msg' => 'gagal mengubah data, harap tunggu beberapa saat.'));

        return redirect()->back();
    }

    $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil!', 'msg' => 'Transfer asset berhasil dirubah.'));

    return redirect()->route('transfer.index');
    }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy($id)
    // {
    //     $transfer = Transfer::find($id);
    //     $transfer->delete();
    //     $transfer->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil!', 'msg' => 'Transfer asset berhasil dihapus.'));

    //     return redirect()->back();
    // }

    public function getLocation(Request $request)
    {
        $locations = Location::select('id', 'loc_site', 'loc_name')->where('loc_site', $request->loc_site)->get();

        return response()->json($locations);
    }

    public function getType()
    {
        $users = Pic::select('pic_nik', 'pic_name', 'pic_status')->where('pic_status', true)->get();
        $sites = Site::select('si_site', 'si_name', 'si_active')->where('si_active', true)->get();

        return response()->json([
            'user' => $users,
            'site' => $sites
        ], 200);
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required',
            'company' => 'required',
        ]);


        if ($request->has('search')) {
            $data = Asset::where('inv_transno', 'LIKE', '%'. $request->search .'%')
                        // ->where('inv_site', $request->siteId)
                        // ->where('inv_loc', $request->locId)
                        // ->where('inv_pic', $request->picId)
                        ->where('inv_company', $request->company)
                        ->where('inv_status', 'ONHAND')
                        ->with('site', 'location', 'user')
                        ->first();

        } else {
            $request->session()->flash('notification', array('type' => 'error', 'title' => 'Gagal!', 'msg' => 'Harap coba beberapa saat'));

            return redirect()->back();
        }
        
        if ($data == null) {
          return response()->json(array('res' => false));
        }

        return response()->json([
          'res' => true,
          'data' => $data,
        ]);
    }

    // public function updateMultiple(Request $request, $id)
    // {
    //     $request->validate([
    //         'trf_site_from' => 'required',
    //         'trf_site_to' => 'required',
    //         'trf_loc_from' => 'required',
    //         'trf_loc_to' => 'required',
    //         'pic_type_from' => 'required',
    //         'trf_pic_from' => 'required',
    //         'pic_type_to' => 'required',
    //         'trf_pic_to' => 'required',
    //         'trf_transdate' => 'required',
    //         'trf_name' => 'required'
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         $transfer = Transfer::with('detail', 'detail.asset')->find($id);

    //         TransferDetail::where('trf_id', $id)->delete();

    //         $req = $request->all();

    //         $transfer->update([
    //             'trf_site_from' => $req['trf_site_from'],
    //             'trf_loc_from' => $req['trf_loc_from'],
    //             'trf_site_to' => $req['trf_site_to'],
    //             'trf_loc_to' => $req['trf_loc_to'],
    //             'pic_type_from' => $req['pic_type_from'],
    //             'pic_type_to' => $req['pic_type_to'],
    //             'trf_pic_from' => $req['trf_pic_from'],
    //             'trf_pic_to' => $req['trf_pic_to'],
    //             'trf_name' => $req['trf_name'],
    //             'trf_transdate' => $req['trf_transdate']
    //         ]);

    //         if ($request->trf_detail_name) {
    //             foreach ($request->trf_detail_name as $key => $value) {
    //                 $items = array(
    //                     'trf_id' => $transfer->id,
    //                     'trf_detail_name' => $request->trf_detail_name[$key],
    //                     'trf_detail_transno' => $request->trf_detail_transno[$key]
    //                 );

    //                 // $inv = Asset::where('inv_transno', $request->trf_detail_transno[$key])->update([
    //                 //     'inv_transno' => $request->trf_detail_transno[$key],
    //                 //     'inv_site' => $req['trf_site_to'],
    //                 //     'inv_loc' => $req['trf_loc_to'],
    //                 //     'inv_pic_type' => $req['pic_type_to'],
    //                 //     'inv_pic' => $req['trf_pic_to']
    //                 // ]);

    //                 TransferDetail::create($items);
    //             }
    //         }
    //         DB::commit();

    //     } catch (\Throwable $th) {
    //         //throw $th;
    //         DB::rollback();

    //         return $th->getMessage();

    //         $request->session()->flash('notification', array('type' => 'error', 'title' => 'Gagal!', 'msg' => 'gagal mengubah data, harap tunggu beberapa saat.'));

    //         return redirect()->back();
    //     }

    //     $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil!', 'msg' => 'Transfer asset berhasil dirubah.'));

    //     return redirect()->route('transfer.index');
    // }

    // public function remove($id)
    // {
    //     $transfer = Transfer::with('detail')->find($id);
    //     // dd($transfer);
    //     $detail = TransferDetail::where('trf_id', $id)->delete();
    //     $transfer->delete();
    //     session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil!', 'msg' => 'Transfer asset berhasil dihapus.'));

    //     return redirect()->back();
    // }

    public function accept(Request $request, $id)
    {
        $transfer = Transfer::with('detail')->find($id);
        try {
            DB::beginTransaction();

            $transfer->update([
                'trf_status' => 'ONHAND',
                'trf_approver_nik' => Auth::user()->usr_nik,
                'trf_approver_name' => Auth::user()->usr_name,
            ]);

            // update inventory
            $asset = Asset::whereIn('inv_transno', [$transfer->detail->trf_detail_transno])->first();
            $asset->update([
              'inv_status' => $transfer->trf_status,
            ]);

            // create transfer detail
            TransferDetail::create([
              'trf_id' => $transfer->id,
              'trf_detail_transno' => $transfer->detail->trf_detail_transno,
              'trf_detail_name' => $transfer->detail->trf_detail_name,
              'trf_detail_status' => $transfer->trf_status,
              'trf_detail_pic' => $transfer->trf_pic_to,
              'trf_detail_desc' => $transfer->detail->trf_detail_desc,
              'trfdtl_approver_nik' => $transfer->trf_approver_nik,
              'trfdtl_approver_name' => $transfer->trf_approver_name,
            ]);

            // create inv_hist
              $history['invhist_transno'] = $asset->inv_transno;
              $history['invhist_inv'] = $asset->id;
              $history['invhist_category'] = $asset->inv_category;
              $history['invhist_site'] = $asset->inv_site;
              $history['invhist_loc'] = $asset->inv_loc;
              $history['invhist_depreciation'] = $asset->inv_depreciation;
              $history['invhist_name'] = $asset->inv_name;
              $history['invhist_pic'] = $asset->inv_pic;
              $history['invhist_obtaindate'] = $asset->inv_obtaindate;
              $history['invhist_price'] = $asset->inv_price;
              $history['invhist_status'] = 'ONHAND';
              $history['invhist_desc'] = $asset->inv_desc;
              $history['invhist_sn'] = $asset->inv_sn;
              $history['invhist_doc_ref'] = $asset->inv_doc_ref;
              $history['invhist_merk'] = $asset->inv_merk;
              $history['invhist_cur_price'] = $asset->inv_current_price;
              $history['invhist_dep_periode'] = $asset->inv_dep_periode;
              $history['invhist_dep_amount'] = $asset->inv_dep_amount;
              $history['invhist_tag'] = $asset->inv_tag;
              $history['invhist_name_short'] = $asset->inv_name_short;
              $history['is_vehicle'] = $asset->is_vehicle;
              
              $invHistory = new InventoryController();
              $invHistory->store($history);

            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            dd($th->getMessage());

            return redirect()->back();
        }

        session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil!', 'msg' => 'Transfer Asset Berhasil di Posting!'));
        return redirect()->back();
    }

    public function print($id)
    {
        $transfer = Transfer::with('detail', 'siteFrom', 'siteTo', 'locFrom','locTo', 'userFrom', 'userTo')->find($id);
        $print = PDF::loadview('transaction.transfer.print', compact('transfer'));
        return $print->stream();
    }
}

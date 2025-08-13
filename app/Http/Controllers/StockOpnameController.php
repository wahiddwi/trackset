<?php

namespace App\Http\Controllers;

use Throwable;
use Carbon\Carbon;
use App\Models\Site;
use App\Models\Asset;
use App\Models\Module;
use App\Models\Location;
use App\Models\StockDetail;
use App\Models\StockMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class StockOpnameController extends Controller
{
  public function __construct() {
    $this->middleware(['permission:create'])->only(['create', 'store']);
    $this->middleware(['permission:update'])->only(['edit', 'update']);
    $this->middleware(['permission:delete'])->only(['delete', 'reject']);
    $this->middleware(['permission:post'])->only(['accept']);
    $this->middleware('permission');
  }
    public function index(Request $request)
    {
        if ($request->ajax()) {
          $model = StockMaster::select('id', 'stock_transno', 'stock_transdate', 'stock_status',
                                      'stock_site', 'stock_loc', 'created_at', 'stock_site_name',
                                      'stock_loc_name')
                                ->with('site', 'loc');

          return DataTables::of($model)
                            ->orderColumn('stock_status', function ($query, $order) {
                              $query->orderByRaw("
                                CASE
                                  WHEN stock_status = 'OPEN' THEN 0
                                  WHEN stock_status = 'CLOSE' THEN 1
                                  WHEN stock_status = 'CANCEL' THEN 2
                                  ELSE 3
                                END $order
                              ");
                            })
                            ->orderColumn('stock_transno', function ($query, $order) {
                              $query->orderBy('created_at', $order);
                            })
                            ->toJson();
        }

        $modules = Module::active()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
        $count = StockMaster::count();
        $menuId = $request->attributes->get('menuId');

        return view('inventory.stock_opname.list', compact('modules', 'count', 'menuId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $selected_site = Session::get('selected_site')->si_company;
      $sites = Site::select('si_site', 'si_name')->whereIn('si_company', [$selected_site])->active()->get();
      return view('inventory.stock_opname.create', compact('sites'));
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
          'stock_site' => 'required|exists:sites,si_site',
          'stock_loc' => 'required',
        ]);

        try {
          
        DB::beginTransaction();

        $asset = Asset::whereIn('inv_site', [$request->stock_site])
                        ->whereIn('inv_loc', [$request->stock_loc])
                        ->with(['site:si_site,si_name', 'pic:id,pic_nik,pic_name'])
                        ->get();

        $date = Carbon::now();
        $site = Site::select('si_site', 'si_name')->where('si_site', $request->stock_site)->active()->first();
        $loc = Location::select('id', 'loc_id', 'loc_site', 'loc_name')->where('id', $request->stock_loc)->active()->first();

        // $stockTransno = IdGenerator::generate(['table' => 'stock_mstr', 'field' => 'stock_transno', 'length' => 16, 'prefix' => 'STO/'. $date->format('y').'/'.month2roman($date->format('m')).'/', 'reset_on_prefix_change' => true]);
        // STO/H01/22/A/00001
        $num = newGetLastDocumentNumber(StockMaster::class, 'stock_transno', array('stock_site' => $request->stock_site), Carbon::parse($request->stock_transdate), 'year', 5, 14, 'stock_transdate', 'stock_transno');
        $stock_transno =  'STO/' . substr($request->stock_site, 0, 3) . '/' . $date->format('y') . '/' . month2roman($date->format('m')) . '/' . str_pad($num, 5, '0', STR_PAD_LEFT);

        $stockMaster = StockMaster::create([
          'stock_transno' => $stock_transno,
          'stock_transdate' => $date->format('d-m-Y'),
          'stock_desc' => $request->stock_desc,
          'stock_status' => 'OPEN',
          'stock_site' => $request->stock_site,
          'stock_site_name' => $site->si_name,
          'stock_loc' => $request->stock_loc,
          'stock_loc_name' => $loc->loc_name,
          'stock_itemttl' => count($asset),
          'stock_found' => 0,
          'stock_opname' => count($asset),
          'stock_counter' => 0,
        ]);

        for ($i=0; $i < $stockMaster->stock_itemttl ; $i++) { 
          $pic_name = $asset[$i]->inv_pic_type != 'user'
                      ? optional($asset[$i]->site)->si_name
                      : optional($asset[$i]->pic)->pic_name;

          StockDetail::create([
            'stockdtl_transno' => $stockMaster->stock_transno,
            'stockdtl_trn_transno' => $asset[$i]->inv_transno, // no asset
            'stockdtl_desc' => $asset[$i]->inv_desc,
            'stockdtl_status' => 'OPNAME',
            'stockdtl_site' => $asset[$i]->inv_site,
            'stockdtl_loc' => $asset[$i]->inv_loc,
            'stockdtl_name' => $asset[$i]->inv_name,
            'stockdtl_pic' => $asset[$i]->inv_pic,
            'stockdtl_pic_name' => $pic_name,
            'stockdtl_obtaindate' => $asset[$i]->inv_obtaindate,
            'stockdtl_type' => 'ITEM', // ADDITIONAL, ITEM
            'stockdtl_price' => $asset[$i]->inv_price,
            'stockdtl_current_price' => $asset[$i]->inv_current_price,
            'stockdtl_order' => $i+1,
          ]);
        }
        
          DB::commit();
        } catch (Throwable $th) {
          DB::rollback();
          Log::error('Error stock opname: ' . $th);
          $request->session()->flash('notification', array('type' => 'error', 'title' => 'Gagal!', 'msg' => 'Silahkan coba beberapa saat.'));
          return redirect()->back();
        }

        return redirect()->route('stock_opname.edit', $stockMaster->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $stock = StockMaster::with('stock_detail', 'site', 'loc')->find($id);

      return view('inventory.stock_opname.show', compact('stock'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
      $stock = StockMaster::with('stock_detail', 'site', 'loc')->find($id);
      // $stockAddons = StockDetail::select('id', 'stockdtl_transno', 'stockdtl_trn_transno', 'stockdtl_desc', 'stockdtl_note', 
      //                             'stockdtl_status', 'stockdtl_site', 'stockdtl_loc', 'stockdtl_name', 'stockdtl_pic', 'stockdtl_pic_name',
      //                             'stockdtl_obtaindate', 'stockdtl_type', 'stockdtl_price', 'stockdtl_current_price', 'stockdtl_order')
      //                             ->with('stock', 'site', 'loc', 'pic')
      //                             ->whereIn('stockdtl_transno', [$stock->stock_transno])
      //                             ->where('stockdtl_type', 'ADDITIONAL')
      //                             ->get();
      
      if ($request->ajax()) {
        $model = StockDetail::with('stock', 'site', 'loc', 'asset', 'pic', 'pic_site')
                              ->where('stockdtl_transno', $stock->stock_transno)
                              ->whereIn('stockdtl_type', ['ITEM']);
                              
        return DataTables::of($model)
                          ->editColumn('stockdtl.site', function (StockDetail $dtl) {
                            return $dtl->site->si_name;
                          })
                          ->editColumn('stockdtl.loc', function (StockDetail $dtl) {
                            return $dtl->loc->loc_name;
                          })
                          ->orderColumn('stockdtl_status', function ($query, $order) {
                            $query->orderByRaw("
                              CASE
                                WHEN stockdtl_status = 'OPNAME' THEN 0
                                WHEN stockdtl_status = 'REMARK' THEN 1
                                WHEN stockdtl_status = 'FOUND' THEN 2
                                ELSE 3
                              END $order
                            ");
                          })
                          ->orderColumn('stockdtl_transdate', function ($query, $order) {
                            $query->orderBy('stockdtl_transdate', $order);
                          })
                          ->toJson();
      }

      return view('inventory.stock_opname.edit', compact('stock'));
    }

    public function update(Request $request, $id)
    {
      $request->validate([
        'stock_id' => 'required',
        'stock_transno' => 'required',
        'stock_transdate' => 'required',
        'stock_site' => 'required',
        'stock_loc' => 'required',
        'stock_desc' => 'required',
        'stock_opname' => 'required',
        'stock_additional' => 'required',
        'stock_found' => 'required',
        'stock_itemttl' => 'required',
      ]);

      $stock = StockMaster::find($request->stock_id);
      $stock->update([
        'stock_transno' => $request->stock_transno,
        'stock_transdate' => $request->stock_transdate,
        'stock_site' => $request->stock_site,
        'stock_loc' => $request->stock_loc,
        'stock_desc' => $request->stock_desc,
        'stock_opname' => $request->stock_opname,
        'stock_additional' => $request->stock_additional,
        'stock_found' => $request->stock_found,
        'stock_itemttl' => $request->stock_itemttl,
      ]);

      $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Stock Opname berhasil diupdate'));
      return redirect()->route('stock_opname.index');
    }

    public function getLocation(Request $request)
    {
      $locations = Location::select('id', 'loc_id', 'loc_site', 'loc_name')->where('loc_site', $request->loc_site)->get();
      // $checkStock = StockMaster::whereIn('stock_loc', [$locations->])
      return response()->json($locations);
    }

    public function checkingLoc(Request $request)
    {
      $getStock = StockMaster::whereIn('stock_loc', [$request->loc])
                              ->where('stock_status', 'OPEN')
                              ->first();

      if (!$getStock) {
        return response()->json(array('res' => true, 'data' => !$getStock));
      } else {
        return response()->json(array('res' => false, 'data' => !$getStock));
      }
    }

    public function checkExistingAsset(Request $request)
    {
      $request->validate([
        'scan' => 'required',
        'transno' => 'required',
      ]);

      if ($request->has('scan')) {
          $checkItems = StockDetail::whereIn('stockdtl_trn_transno', [$request->scan])
                                    ->where('stockdtl_type', 'ITEM')
                                    ->where('stockdtl_status', 'FOUND')
                                    ->exists();
          if ($checkItems) {
            return response()->json(array('res' => false, 'msg' => 'No. Asset tersebut sudah melakukan stock opname.'));
          }

          $checkAdditionalItem = StockDetail::whereIn('stockdtl_trn_transno', [$request->scan])
                                            ->where('stockdtl_type', 'ADDITIONAL')
                                            ->exists();
          if ($checkAdditionalItem) {
            return response()->json(array('res' => false, 'msg' => 'Asset tambahan sudah ditemukan.'));
          }
        return;
      }
    }

    public function scan(Request $request)
    {
      $request->validate([
        'scan' => 'required',
        'transno' => 'required',
      ]);

        if ($request->has('scan')) {
          // data barang
          $detail = StockDetail::with('stock', 'site', 'loc', 'asset')
                                ->whereIn('stockdtl_trn_transno', [$request->scan])
                                ->first();

          if (!is_null($detail) && $detail->stockdtl_status != 'FOUND') {
            // update sto_dtl (status)
            $detail->update([
              'stockdtl_status' => 'FOUND',
            ]);

            // update stock (count stock_found, stock_opname)
            $detail->stock->update([
              'stock_found' => $detail->stock->stock_found + 1,
              'stock_opname' => $detail->stock->stock_opname - 1,
              'stock_counter' => $detail->stock->stock_counter + 1,
              'stock_itemttl' => $detail->stock->stock_found + $detail->stock->stock_opname + $detail->stock->stock_additional,
            ]);

            return response()->json([
              'res' => true,
              'data' => $detail,
              'stock_found' => $detail->stock->stock_found,
              'stock_opname' => $detail->stock->stock_opname,
              'stock_counter' => $detail->stock->stock_counter,
              'stock_additional' => $detail->stock->stock_additional,
              'msg' => 'Data Asset ditemukan.',
            ]);
          }
          
          if (is_null($detail)) {
            # code...
            $asset = Asset::select('inv_transno', 'inv_site', 'inv_loc', 'inv_name', 'inv_pic', 'inv_pic_type',
                                    'inv_obtaindate', 'inv_price', 'inv_current_price', 'inv_desc')
                            ->with('pic', 'site')
                            ->whereIn('inv_transno', [$request->scan])
                            ->first();

            $invPic = $asset->inv_pic_type != 'user'
            ? optional($asset->site)->si_name
            : optional($asset->pic)->pic_name;

            if (is_null($asset)) {
              $payload['stockdtl_transno'] = $request->transno;
              $payload['stockdtl_trn_transno'] = $request->scan;
              $payload['stockdtl_desc'] = NULL;
              $payload['stockdtl_status'] = 'OPNAME';
              $payload['stockdtl_site'] = NULL;
              $payload['stockdtl_loc'] = NULL;
              $payload['stockdtl_name'] = NULL;
              $payload['stockdtl_pic'] = NULL;
              $payload['stockdtl_pic_name'] = NULL;
              $payload['stockdtl_obtaindate'] = NULL;
              $payload['stockdtl_type'] = 'ADDITIONAL';
              $payload['stockdtl_price'] = NULL;
              $payload['stockdtl_current_price'] = Null;
            } else {
              # create sto_dtl
              $payload['stockdtl_transno'] = $request->transno;
              $payload['stockdtl_trn_transno'] = $asset->inv_transno;
              $payload['stockdtl_desc'] = $asset->inv_desc;
              $payload['stockdtl_status'] = 'OPNAME';
              $payload['stockdtl_site'] = $asset->inv_site;
              $payload['stockdtl_loc'] = $asset->inv_loc;
              $payload['stockdtl_name'] = $asset->inv_name;
              $payload['stockdtl_pic'] = $asset->inv_pic;
              $payload['stockdtl_pic_name'] = $invPic;
              $payload['stockdtl_obtaindate'] = $asset->inv_obtaindate;
              $payload['stockdtl_type'] = 'ADDITIONAL';
              $payload['stockdtl_price'] = $asset->inv_price;
              $payload['stockdtl_current_price'] = $asset->inv_current_price;
            }

            $stock = StockMaster::whereIn('stock_transno', [$payload['stockdtl_transno']])->first();

            $stockdtl = StockDetail::create([
              'stockdtl_transno' => $payload['stockdtl_transno'],
              'stockdtl_trn_transno' => $payload['stockdtl_trn_transno'],
              'stockdtl_desc' => $payload['stockdtl_desc'],
              'stockdtl_status' => $payload['stockdtl_status'],
              'stockdtl_site' => $payload['stockdtl_site'],
              'stockdtl_loc' => $payload['stockdtl_loc'],
              'stockdtl_name' => $payload['stockdtl_name'],
              'stockdtl_pic' => $payload['stockdtl_pic'],
              'stockdtl_obtaindate' => $payload['stockdtl_obtaindate'],
              'stockdtl_type' => $payload['stockdtl_type'],
              'stockdtl_price' => $payload['stockdtl_price'],
              'stockdtl_current_price' => $payload['stockdtl_current_price'],
            ]);

            $count_stock_opname = StockDetail::whereIn('stockdtl_status', ['OPNAME'])
                                             ->where('stockdtl_type', 'ITEM')
                                             ->count();

            $stock->update([
              'stock_additional' => $stock->stock_additional + 1, // Tambahkan 1 ke jumlah item tambahan
              'stock_counter' => $stock->stock_counter + 1,
              'stock_opname' => $count_stock_opname,
              'stock_itemttl' => $stock->stock_itemttl + 1, // Tambahkan 1 ke jumlah item keseluruhan
            ]);

            return response()->json([
              'res' => false,
              'data' => $stockdtl,
              'stock_additional' => $stock->stock_additional,
              'stock_counter' => $stock->stock_counter,
              'stock_opname' => $stock->stock_opname,
              'msg' => 'Data asset tambahan ditemukan.',
            ]);
          }

        } else {
          # code...
          $request->session()->flash('notification', array('type' => 'error', 'title' => 'Gagal!', 'msg' => 'Harap coba beberapa saat'));
          return redirect()->back();
        }
    }

    public function approve(Request $request)
    {
      $detail = StockDetail::find($request->id);
      if (!$detail) {
        return response(array('res' => false));
      }

      $detail->stockdtl_note = $request->note;
      $detail->stockdtl_status = 'REMARK';

      $detail->save();
      return response(array('res' => true));
    }

    public function getApprovalNote($id)
    {
      $stockDetail = StockDetail::find($id);
      return response(array('res' => true, 'data' => $stockDetail));
    }

    public function accept($id)
    {
      $stock = StockMaster::with('stock_detail')->find($id);

      try {
        # code...
        DB::beginTransaction();

        if (!$stock) {
          return response(array('res' => false, 'msg' => 'Data tidak ditemukan!.'));
        }

        if ($stock->stock_status != 'OPEN') {
          return response(array('res' => false, 'msg' => 'INVALID STATUS.'));
        }

        $hasStockdtlStatus = $stock->stock_detail->contains (function ($detail) {
          return $detail->stockdtl_status === 'OPNAME';
        });

        if ($hasStockdtlStatus) {
          return response(array('res' => false, 'msg' => 'Tidak dapat disetujui, masih ada item dengan status OPNAME.'));
        }

        $stock->stock_status = 'CLOSE';
        $stock->save();

        DB::commit();
      } catch (\Throwable $th) {
        //throw $th;
        Log::error($th);
        DB::rollback();
        return response(array('res' => false, 'msg' => 'Terjadi kesalahan, Silahkan coba beberapa saat lagi!.'));
      }
      return response(array('res' => true, 'msg' => 'Stock Opname berhasil diterima.'));
    }

    public function reject($id)
    {
      $stock = StockMaster::find($id);
      $stock->stock_status = 'CANCEL';
      $stock->save();

      return array('res' => true);
    }

    public function getAdditionalItems(Request $request, $id)
    {
      $stock = StockMaster::find($id);
      if ($request->ajax()) {
        $model = StockDetail::select('id', 'stockdtl_trn_transno', 'stockdtl_name', 'stockdtl_pic', 
                                    'stockdtl_pic_name', 'stockdtl_price', 'stockdtl_note', 'stockdtl_order',
                                    'stockdtl_status')
                                    ->where('stockdtl_transno', $stock->stock_transno)
                                    ->where('stockdtl_type', 'ADDITIONAL');

        return DataTables::of($model)
                          ->editColumn('stockdtl.pic', function (StockDetail $dtl) {
                            if ($dtl->stockdtl_pic == $dtl->pic->pic_nik) {
                              return $dtl->pic->pic_name;
                            } else {
                              return $dtl->site->si_name;
                            }
                          })
                          ->orderColumn('stockdtl_status', function ($query, $order) {
                            $query->orderByRaw("
                              CASE
                                WHEN stockdtl_status = 'OPNAME' THEN 0
                                WHEN stockdtl_status = 'REMARK' THEN 1
                                ELSE 3
                              END $order
                            ");
                          })
                          ->orderColumn('stockdtl_transdate', function ($query, $order) {
                            $query->orderBy('stockdtl_transdate', $order);
                          })
                        ->toJson();
      }
    }
}

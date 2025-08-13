<?php

namespace App\Http\Controllers\Transaction;

use Exception;
use Throwable;
use Carbon\Carbon;
use App\Models\Pic;
use App\Models\Site;
use App\Models\Asset;
use App\Models\Module;
use App\Models\InvHist;
use App\Models\Category;
use App\Models\Transfer;
use Illuminate\Http\Request;
use App\Models\RequestDetail;
use App\Models\RequestMaster;
use App\Models\TransferDetail;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Journal\JournalController;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RequestController extends Controller
{
  public function __construct() {
    $this->middleware(['permission']);
    $this->middleware(['permission:update'])->only(['close_spb']);
  }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if ($request->ajax()) {
        # code...
        $model = RequestMaster::select('id', 'req_spb', 'req_transdate', 'req_status', 'req_site')
                                ->with('detail', 'site');

        return DataTables::of($model)
                          ->editColumn('req_site', function (RequestMaster $data) {
                            return $data->site->si_name;
                          })
                          ->orderColumn('req_status', function ($query, $order) {
                            $query->orderByRaw("
                              CASE
                                WHEN req_status = 'OPEN' THEN 0
                                WHEN req_status = 'CLOSE' THEN 1
                                ELSE 2
                              END $order
                            ");
                          })
                          ->orderColumn('req_transdate', function ($query, $order) {
                            $query->orderBy('req_transdate', $order);
                          })
                          ->orderColumn('id', function ($query, $order) {
                            $query->orderBy('id', $order);
                          })
                          ->toJson();
      }

      $modules = Module::where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
      $count = RequestMaster::count();
      // $menuId = $request->attributes->get('menuId');

      return view('transaction.request.list', compact('modules', 'count'));
    }

    public function close_spb($id)
    {
      $req = RequestMaster::with('detail', 'site')->findOrFail($id);
      $selected_company = Session::get('selected_site')->si_company;

      $asset = Asset::select('id', 'inv_transno', 'inv_name', 'inv_company', 'inv_site', 'inv_status', 'inv_merk')
                      ->whereIn('inv_company', [$selected_company])
                      ->where('inv_status', 'ONHAND')
                      ->get();

      $sites = Site::select('si_site', 'si_name', 'si_company')
                    ->whereIn('si_company', [$selected_company])
                    ->where('si_active', true)
                    ->get();

      $users = Pic::select('pic_nik', 'pic_name', 'pic_status')
                    ->where('pic_status', true)
                    ->get();

      return view('transaction.request.transfer', compact('req', 'selected_company', 'asset', 'users', 'sites'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        'req_id' => 'required|integer',
        'trf_company' => 'required',
        'trf_transdate' => 'required',
        'trf_desc' => 'nullable|max:255',
        'trfdtl_asset_no.*' => 'required',
        'trfdtl_order.*' => 'required',
        'trfdtl_asset_name.*' => 'required',
        'trfdtl_item.*' => 'required',
        'trfdtl_desc.*' => 'nullable|max:255',
      ]);

      try {
        //code...
        DB::beginTransaction();
        // dd($request->all());
        // dd(count($request->trfdtl_item));

        // $req = RequestMaster::with('detail')->findOrFail($request->req_id);
        // dd($req);

        // for ($i=0; $i < count($request->trfdtl_item) ; $i++) { 
        //   # code...
        //   $detail = RequestDetail::where('reqdtl_id', $)
        // }
        
        $detail = RequestDetail::where('reqdtl_id', $request->req_id)->get();
        dd($detail);

        DB::commit();
      } catch (Throwable $th) {
        //throw $th;
        Log::error('Error Request Asset : ' . $th);
        DB::rollback();

        $request->session()->flash('notification', array('type' => 'Error!', 'title' => 'Gagal!', 'msg' => 'Terjadi kesalahan, harap coba kembali.'));
        return redirect()->back();
      }

      $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Berhasil transfer permintaan asset.'));
      return redirect()->route('transaction.request.list');
    }

    public function buyAsset(Request $request)
    {
      $data = $request->all();
      return view('transaction.request.buy', compact('data'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $req = RequestMaster::with('detail', 'site')->find($id);
      
      return response()->json([
        'res' => true,
        'result' => $req
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
        //
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
        //
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

    public function transferProcess(Request $request)
    {
      $request->validate([
        'req_id' => 'required|integer',
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
        'trfdtl_order.*' => 'required',
        'trfdtl_asset_name.*' => 'required',
        'trfdtl_item.*' => 'required',
        'trfdtl_desc.*' => 'nullable|max:255',
      ]);

      try {
        DB::beginTransaction();

        $sendPayload = $this->updateAssetRequest($request->all());

        $date = Carbon::parse($request->trf_transdate);

        $num = newGetLastDocumentNumber(Transfer::class, 'trf_transno', array('trf_company' => $request->trf_company), $date, 'year', 5, 14, 'trf_transdate', 'trf_transno');
        $trf_transno =  'TRF/' . substr($request->trf_company, 0, 3) . '/' . $date->format('y') . '/' . month2roman($date->format('m')) . '/' . str_pad($num, 5, '0', STR_PAD_LEFT);

        $line_count = count($request->trfdtl_order);
        
        $transfer = Transfer::create([
          'trf_transno' => $trf_transno,
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
          'trf_desc' => $request->trf_desc,
          'trf_status' => 'DRAFT',
          'trf_count' => $line_count,
          'trf_created_name' => Auth::user()->usr_name,
          'trf_updated_name' => Auth::user()->usr_name,
          'trf_spb' => $sendPayload->req_spb,
        ]);

        for ($i=0; $i < $line_count ; $i++) { 
          # code...
          TransferDetail::create([
            'trfdtl_id' => $transfer->id,
            'trfdtl_transno' => $request->trfdtl_asset_no[$i],
            'trfdtl_name' => $request->trfdtl_asset_name[$i],
            'trfdtl_status' => $transfer->trf_status,
            'trfdtl_desc' =>  $request->trfdtl_desc[$i],
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
            'trfdtl_created_name' => $transfer->trf_created_name,
            'trfdtl_updated_name' => $transfer->trf_updated_name,
            'trfdtl_itemcode' => $request->trfdtl_item[$i],
            'trfdtl_spb' => $transfer->trf_spb,
          ]);

        }

        DB::commit();
      } catch (Throwable $th) {
        //throw $th;
        Log::error('Error Request Asset : ' . $th);
        DB::rollback();

        $request->session()->flash('notification', array('type' => 'Error!', 'title' => 'Gagal!', 'msg' => 'Terjadi kesalahan, harap coba kembali.'));
        return redirect()->back();
      }

      $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Berhasil transfer permintaan asset.'));
      return redirect()->route('asset_request.index');
    }

    // public function updateAssetRequest($payload)
    // {
    //   // Hitung jumlah transfer per item
    //   $transferCounts = [];
    //   foreach ($payload['trfdtl_item'] as $itemCode) {
    //     if (!isset($transferCounts[$itemCode])) {
    //       $transferCounts[$itemCode] = 0;
    //     }
    //     $transferCounts[$itemCode]++;
    //   }

    //   $req = RequestMaster::with('detail')->findOrFail($payload['req_id']);

    //   // Update qty_send pada detail permintaan
    //   foreach ($req->detail as $detail) {
    //     $itemCode = $detail->reqdtl_code;
    //     if (isset($transferCounts[$itemCode])) {
    //       $detail->reqdtl_qty_send += $transferCounts[$itemCode];
    //       $detail->save();
    //     }
    //   }

    //   $tempDetails = $req->detail->toArray();
        
    //   $req->detail->each->delete();

    //   foreach ($tempDetails as $detail) {
    //     RequestDetail::create([
    //       'reqdtl_id' => $detail['reqdtl_id'],
    //       'reqdtl_code' => $detail['reqdtl_code'],
    //       'reqdtl_item' => $detail['reqdtl_item'],
    //       'reqdtl_uom' => $detail['reqdtl_uom'],
    //       'reqdtl_qty' => $detail['reqdtl_qty'],
    //       'reqdtl_qty_approve' => $detail['reqdtl_qty_approve'],
    //       'reqdtl_qty_send' => $detail['reqdtl_qty_send'],
    //       'reqdtl_line' => $detail['reqdtl_line'],
    //     ]);
    //   }

    //   return;
    // }

    public function updateAssetRequest($payload)
    {
      // Hitung jumlah transfer per item
      $transferCounts = [];
      foreach ($payload['trfdtl_item'] as $itemCode) {
        if (!isset($transferCounts[$itemCode])) {
          $transferCounts[$itemCode] = 0;
        }
        $transferCounts[$itemCode]++;
      }

      $req = RequestMaster::with('detail')->findOrFail($payload['req_id']);

      // Update qty_send pada detail permintaan
      foreach ($req->detail as $detail) {
        $itemCode = $detail->reqdtl_code;
        if (isset($transferCounts[$itemCode])) {
          $detail->reqdtl_qty_send += $transferCounts[$itemCode];
          $detail->save();
        }
      }

      $tempDetails = $req->detail->toArray();
        
      $req->detail->each->delete();

      foreach ($tempDetails as $detail) {
        RequestDetail::create([
          'reqdtl_id' => $detail['reqdtl_id'],
          'reqdtl_code' => $detail['reqdtl_code'],
          'reqdtl_item' => $detail['reqdtl_item'],
          'reqdtl_uom' => $detail['reqdtl_uom'],
          'reqdtl_qty' => $detail['reqdtl_qty'],
          'reqdtl_qty_approve' => $detail['reqdtl_qty_approve'],
          'reqdtl_qty_send' => $detail['reqdtl_qty_send'],
          'reqdtl_line' => $detail['reqdtl_line'],
        ]);
      }

      return $req;
    }

    public function purchase_asset($id)
    {
      $req = RequestMaster::with('detail', 'trf_detail', 'trf')->findOrFail($id);
      return response()->json(array('res' => true, 'data' => $req));
    }

    public function purchaseProcess(Request $request)
    {

      $request->validate([
        'req_spb' => 'required',
        'req_transdate' => 'required',
        'trfdtl_order.*' => 'required',
        'trfdtl_itemcode.*' => 'required',
        'trfdtl_item.*' => 'required',
        'trfdtl_qty_approve.*' => 'required',
        'trfdtl_qty_send.*' => 'required',
        'trfdtl_qty_purchase.*' => 'required',
      ]);
      
      try {        
        DB::beginTransaction();

        $allItemRequests = array_sum($request->trfdtl_qty_approve);
        $qtyTrf = array_sum($request->trfdtl_qty_send);
        $qtyBuy = array_sum($request->trfdtl_qty_purchase);
        $getAllQtyBuy = ($allItemRequests - $qtyTrf);
        $qtyRemaining = ($qtyTrf + $qtyBuy);

        if ($allItemRequests == $qtyRemaining) {
          if ($getAllQtyBuy == 0) {
            # CLOSE ITEM REQUEST
            $spb_no = array(
              'spbno' => $request->req_spb
            );

            Log::info('Kirim SPB ke RGAPI : ', ['spb' => $spb_no]);
  
            $response = $this->close_process($spb_no);
  
            Log::info('Response dari RGAPI : ', ['response' => $response]);
          }

          # qty request == qty sisa(qty trf + qty buy)
          // update request master
          $req = RequestMaster::with('detail')->findOrFail($request->req_id);
          $req->req_status = 'CLOSE';
          $req->save();

          //update request detail
          $req->detail->each->delete();

          $reqDetail = array();
          for ($i=0; $i < $request->line_count; $i++) {
            $reqDetail = RequestDetail::create([
              'reqdtl_id' => $req->id,
              'reqdtl_code' => $request->trfdtl_code[$i],
              'reqdtl_item' => $request->trfdtl_itemname[$i],
              'reqdtl_uom' => $request->trfdtl_uom[$i],
              'reqdtl_qty' => $request->trfdtl_qty[$i],
              'reqdtl_qty_approve' => $request->trfdtl_qty_approve[$i],
              'reqdtl_qty_send' => $request->trfdtl_qty_send[$i],
              'reqdtl_qty_purchase' => $request->trfdtl_qty_purchase[$i],
              'reqdtl_line' => $request->trfdtl_order[$i],
            ]);

            if ($request->trfdtl_qty_send[$i] > 0) {
              # jika Transfer
              $transfer = Transfer::with('detail')->where('trf_spb', $req->req_spb)->get();

              foreach($transfer as $trf) {
                // update transfer
                $trf->trf_status = 'TRF';
                $trf->trf_approver_name = Auth::user()->usr_name;
                $trf->trf_approver_nik = Auth::user()->usr_nik;
                $trf->save();

                foreach ($trf->detail as $dtl) {
                  # update transfer detail
                  $dtl->trfdtl_status = $trf->trf_status;
                  $dtl->trfdtl_approver_nik = Auth::user()->usr_nik;
                  $dtl->trfdtl_approver_name = Auth::user()->usr_name;
                  $dtl->save();

                  // update asset (inv_mstr)
                  $asset = Asset::whereIn('inv_transno', [$dtl->trfdtl_transno])->first();

                  $asset->inv_status = 'ONHAND';
                  $asset->inv_site = $trf->trf_site_to;
                  $asset->inv_loc = $trf->trf_loc_to;
                  $asset->inv_pic_type = $trf->trf_pic_type_to;
                  $asset->inv_pic = $trf->trf_pic_to;
                  $asset->save();

                  // create History
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
                }

                // journal transfer
                $date = Carbon::parse($trf->trf_transdate);
                $category = Category::find($asset->inv_category);
  
                $journalDtl = array();
                if ($asset->inv_accumulate_dep == 0) {
                  # transfer (belum depresiasi)
                  array_push(
                    $journalDtl,
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
                      'jld_account' => $category->cat_asset, // Akun asset
                      'jld_amount' => $asset->inv_price, // harga asset
                      'jld_cc' => '',
                      'jld_rmks' => $asset->inv_desc,
                    ),
                  );
                } else {
                  # code...
                  array_push(
                    $journalDtl,
                    array(
                      'jld_type' => 'DEBIT',
                      'jld_periode' => $date->format('Ym'),
                      'jld_site' => $trf->trf_site_to, // site tujuan
                      'jld_account' => $category->cat_asset, // Akun gagal
                      'jld_amount' => $asset->inv_price, // harga asset
                      'jld_cc' => '',
                      'jld_rmks' => $asset->inv_desc,
                    ),
                    array(
                      'jld_type' => 'DEBIT',
                      'jld_periode' => $date->format('Ym'),
                      'jld_site' => $trf->trf_site_to, // site tujuan
                      'jld_account' => $category->cat_accumulate_depreciation, // Akun akumulasi depreasi
                      'jld_amount' => $asset->inv_accumulate_dep, // akumulasi depresiasi
                      'jld_cc' => '',
                      'jld_rmks' => $asset->inv_desc,
                    ),
                    array(
                      'jld_type' => 'KREDIT',
                      'jld_periode'=> $date->format('Ym'),
                      'jld_site' => $trf->trf_site_from, // site assal
                      'jld_account' => $category->cat_asset, // akun asset
                      'jld_amount' => $asset->inv_price, // harga asset
                      'jld_cc' => '',
                      'jld_rmks' => $asset->inv_desc,
                    ),
                    array(
                      'jld_type' => 'KREDIT',
                      'jld_periode' => $date->format('Ym'),
                      'jld_site' => $trf->trf_site_to, // site asal
                      'jld_account' => $category->cat_asset, // cat asset
                      'jld_amount' => $asset->inv_accumulate_dep, // akumulasi depresiasi
                      'jld_cc' => '',
                      'jld_rmks' => $asset->inv_desc,
                    ),
                  );
                }
  
                if ($journalDtl) {
                  # journal header
                  $data['jl_period'] = $date->format('Ym');
                  $data['jl_eff_date'] = $date->format('Ym');
                  $data['hcompany'] = 'H001';
                  $data['jl_company'] = $asset->inv_company;
                  $data['jl_site'] = $asset->inv_site;
                  $data['jl_ref'] = $asset->inv_doc_ref;
                  $data['rmks'] = 'Transfer Asset';
                  $data['jl_rowttl'] = count($journalDtl);
                  $data['user'] = Auth::user()->usr_nik;
                  $data['detail'] = $journalDtl;
  
                  Log::info('Payload Journal : '. json_encode($data));
  
                  // $journal = new JournalController();
                  // $journal->journalAsset($data);
                }
              }
            }

            if ($request->trfdtl_qty_purchase[$i] > 0) {
              # Jika Beli
              $irDetail = array();
              for ($j=0; $j < $request->line_count; $j++) { 
                # item_detail
                array_push(
                  $irDetail,
                  array(
                    'irdtl_transno' => $request->req_spb,
                    'irdtl_line' => $request->trfdtl_order[$j],
                    'irdtl_item' => $request->trfdtl_code[$j],
                    'irdtl_item_name' => $request->trfdtl_itemname[$j],
                    'irdtl_qty' => $request->trfdtl_qty[$j],
                    'irdtl_uom' => $request->trfdtl_uom[$j],
                    'irdtl_qty_approve' => $request->trfdtl_qty_approve[$j],
                    'irdtl_qty_send' => $request->trfdtl_qty_send[$j],
                    'irdtl_qty_buy' => $request->trfdtl_qty_purchase[$j],
                  )
                );
              }

              $ir = array(
                'ir_transno' => $request->req_spb,
                'ir_status' => 'APPROVED',
                'ir_asset_status' => 'POST',
                'ir_site' => $req->req_site,
                'ir_company' => $req->req_company,
                'ir_line' => $request->line_count,
                'ir_approver_nik' => Auth::user()->usr_nik,
                'detail' => $irDetail,
              );

              Log::info('Kirim payload ke RGAPI : ', ['payload' => $ir]);

              $response = $this->purchase_item($ir);

              Log::info('Response dari RGAPI : ', ['response' => $response]);
            }
          }

        }

        if ($allItemRequests < $qtyRemaining) {
          # code...
          session()->flash('notification', array('type' => 'Error!', 'title' => 'Gagal!', 'msg' => 'Gagal melakukan pembelian asset.'));
          return redirect()->back();
        }

        DB::commit();
      } catch (\Throwable $th) {
        Log::error('Error Beli Asset : ' .$th);
        DB::rollBack();
        
        return response()->json([
            'res' => false,
            'message' => 'Terjadi kesalahan: ' . $th
        ]);
      }

      session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'SPB berhasil diapprove.'));
      return array('res' => true);
    }

    public function purchase_item($payload)
    {
      $apiUrl = config('app.api_url') . '/purchase/purchase_item';
      $token = env('API_KEY_INTERNAL');
  
      try {
        //code...
        // send payload to API
        $res = Http::withToken($token)->post($apiUrl, $payload);
        $statusCode = $res->status();
        $responseBody = $res->json();

        // Logging request
        Log::info('Mengirim payload ke RGAPI', [
          'url' => $apiUrl,
          'status' => $statusCode,
          'payload' => $payload,
          'response' => $responseBody,
        ]);
  
        if ($statusCode == 500) {
          # code...
          Log::error('Gagal menghubungi RGAPI: Internal Server Error', [
            'status' => $statusCode,
            'response' => $responseBody
          ]);
          throw new HttpException(500, "ERROR API: Internal Server Error");
        }
  
        // Jika response tidak berhasil (result === false)
        if (!$responseBody || !isset($responseBody['result']) || $responseBody['result'] === false) {
          $errorMessage = $responseBody['res_msg'] ?? 'Unknown error';
          Log::error('Gagal mengirim data ke RGAPI', [
              'status' => $statusCode,
              'error' => $errorMessage
          ]);
          throw new HttpException(500, "ERROR API: " . $errorMessage);
        }
  
        // Jika berhasil, kembalikan response
        return $responseBody;
  
      } catch (Exception $e) {
        Log::error('Exception saat mengirim payload ke RGAPI', [
            'message' => $e,
            'payload' => $payload
        ]);
  
        throw new HttpException(500, "Terjadi kesalahan: " . $e->getMessage());
      }
    }

    public function close_process($payload)
    {
      $apiUrl = config('app.api_url') . '/purchase/close_spb';
      $token = config('app.internal_api_key');
      
      try {
        $res = Http::withToken($token)->post($apiUrl, $payload);
        $statusCode = $res->status();
        $resBody = $res->json();

        Log::info('Mengirim SPB ke RGAPI', [
          'url' => $apiUrl,
          'status' => $statusCode,
          'payload' => $payload,
          'response' => $resBody,
        ]);

        if ($statusCode == 500) {
          Log::error('Gagal menghubungi RGAPI: Internal Server Error', [
            'status' => $statusCode,
            'response' => $resBody,
          ]);

          throw new HttpException(500, "Error API : Internal Server error.");
        }

        if (!$resBody || !isset($resBody['result']) || $resBody['result'] === false) {
          # jika response tidak berhasil (result === false)
          $errorMsg = $resBody['res_msg'] ?? 'Unknown error';
          Log::error('Gagal mengirim data ke RGAPI', [
            'status' => $statusCode ,
            'error' => $errorMsg
          ]);
          throw new HttpException(500, "Error API: " . $errorMsg);
        }

        // jika berhasil, kembalikan response
        return $resBody;
      } catch (\Exception $th) {
        //throw $th;
        Log::error('Exception saat mengirim payload ke RGAPI', [
          'message' => $e->getMessage(),
          'payload' => $data,
        ]);

        throw new HttpException(500, "Terjadi kesalahan : " . $e->getMessage());
      }
    }
}

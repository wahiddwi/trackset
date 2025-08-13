<?php

namespace App\Http\Controllers\Transaction;

use Throwable;
use Carbon\Carbon;
use App\Models\Site;
use App\Models\Asset;
use App\Models\Module;
use App\Models\InvHist;
use App\Models\Selling;
use App\Models\Category;
use App\Models\Customer;
use App\Models\GeneralParam;
use Illuminate\Http\Request;
use App\Models\SellingDetail;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Journal\JournalController;

class SellingController extends Controller
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
        $model = Selling::select('id', 'sell_no', 'sell_transdate', 'sell_cust_name', 'sell_total_price', 'sell_status')
                          ->where('sell_company', $selected_company)
                        ->with('detail', 'customer');
                  // ->where('sell_status', 'RSV');

        return DataTables::of($model)
                          ->orderColumn('sell_status', function ($query, $order) {
                            $query->orderByRaw("
                              CASE
                                WHEN sell_status = 'RSV' THEN 0
                                WHEN sell_status = 'CANCEL' THEN 1
                                WHEN sell_status = 'SELL' THEN 2
                                ELSE 3
                              END $order
                            ");
                          })
                          ->orderColumn('sell_transdate', function ($query, $order) {
                            $query->orderBy('sell_transdate', $order);
                          })
                          ->toJson();
      }
      $modules = Module::isSuper()->active()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
      $count = Selling::where('sell_company', $selected_company)
                        ->count();
      $menuId = $request->attributes->get('menuId');

      return view('transaction.selling.list', compact('count', 'modules', 'menuId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $selected_company = Session::get('selected_site')->si_company;
      $customer = Customer::select('id', 'cust_no', 'cust_name', 'cust_type', 'cust_addr', 'cust_telp', 'cust_wa', 'cust_email')
                            ->active()
                            ->get();
      $asset = Asset::select('id', 'inv_transno', 'inv_name', 'inv_status')
                      ->whereIn('inv_company', [$selected_company])
                      ->where('inv_status', 'ONHAND')
                      ->get();

      return view('transaction.selling.create', compact('customer', 'asset', 'selected_company'));
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
        'sell_transdate' => 'required',
        'sell_cust' => 'required',
        'sell_desc' => 'required|max:255',
        'dtl_asset_transno.*' => 'required',
        'dtl_asset_name.*' => 'required',
        'dtl_asset_inv_price.*' => 'required', // harga asset saat ini
        'dtl_asset_desc.*' => 'nullable',
        'dtl_sell_price.*' => 'required', // harga jual aset
      ]);
      try {
        DB::beginTransaction();
        $date = Carbon::parse($request->sell_transdate);
        $num = newGetLastDocumentNumber(Selling::class, 'sell_no', array('sell_company' => $request->sell_company), $date, 'year', 5, 15, 'sell_transdate', 'sell_no');
        // SELL/GJB/24/B/00001
        $sell_no = 'SELL/' . substr($request->sell_company, 0, 3) . '/' . $date->format('y') . '/' . month2roman($date->format('m')) . '/' . str_pad($num, 5, '0', STR_PAD_LEFT);

        $qtyCount = count($request->dtl_asset_transno);
        $amountDepPrice = array_sum($request->dtl_asset_inv_price);
        $amountSellPrice = array_sum($request->hidden_sell_price);
        // store sell_mstr table
        $sell = Selling::create([
          'sell_no' => $sell_no,
          'sell_transdate' => $request->sell_transdate,
          'sell_cust_id' => $request->sell_cust,
          'sell_cust_name' => $request->cust_name,
          'sell_cust_no' => $request->cust_no,
          'sell_cust_addr' => $request->cust_addr,
          'sell_cust_telp' => $request->cust_telp,
          'sell_cust_wa' => $request->cust_wa,
          'sell_cust_email' => $request->cust_email,
          'sell_status' => 'RSV', // RSV, SELL
          'sell_company' => $request->sell_company,
          'sell_site' => $request->sell_site,
          'sell_desc' => $request->sell_desc,
          'sell_qty_item' => $qtyCount,
          'sell_total_price' => $amountSellPrice, // jumlah harga jual
          'sell_amount_dep_price' => $amountDepPrice, // total harga yang sudah di depresiasi
          'sell_created_name' => Auth::user()->usr_name,
          'sell_created_nik' => Auth::user()->usr_nik,
        ]);

        $asset = Asset::whereIn('inv_transno', $request->dtl_asset_transno)
                        ->where('inv_status', 'ONHAND')
                        ->get();

        foreach ($asset as $key => $item) {
          // store sell_detail table
          SellingDetail::create([
            'selldtl_asset_id' => $item->id,
            'selldtl_id' => $sell->id,
            'selldtl_transno' => $request->dtl_asset_transno[$key],
            'selldtl_asset_name' => $request->dtl_asset_name[$key],
            'selldtl_transdate' => $request->sell_transdate,
            'selldtl_acc_dep' => $item->inv_accumulate_dep,
            'selldtl_price' => $request->hidden_sell_price[$key] == 'null' ? 0 : $request->hidden_sell_price[$key], // harga jual
            'selldtl_dep_price' => $request->dtl_asset_inv_price[$key] == 'null' ? 0 : $request->dtl_asset_inv_price[$key], // harga yang sudah didepresiasi
            'selldtl_desc' => $request->dtl_sell_desc[$key],
            'selldtl_status' => $sell->sell_status,
            'selldtl_order' => $key+1,
          ]);

          #updated in table inv_mst
          $inventory = Asset::find($item->id);
          $inventory->inv_status = 'RSV'; // RSV, ONHAND
          $inventory->save();
        }
        
        DB::commit();
      } catch (Throwable $th) {
        //throw $th;
        Log::error('Error: ' . $th->getMessage());
        DB::rollback();
        $request->session()->flash('notification', array('type' => 'error', 'title' => 'Gagal!', 'msg' => 'Harap coba beberapa saat'));
        return redirect()->back();
      }
      $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Penjualan berhasil ditambahkan!'));
      return redirect()->route('selling.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $sell = Selling::select('id', 'sell_no', 'sell_transdate', 'sell_status', 'sell_desc', 
                              'sell_cust_name', 'sell_cust_no', 'sell_cust_telp', 'sell_cust_wa',
                              'sell_cust_addr')
                      ->with('detail:id,selldtl_id,selldtl_order,selldtl_transno,selldtl_asset_name,selldtl_price,selldtl_desc')->find($id);
      if (is_null($sell)) {
        return response(array('res' => false));
      }

      return response()->json([
        'res' => true,
        'result' => $sell
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
      $sell = Selling::with('detail', 'customer', 'company', 'site')->find($id);
      $selected_company = Session::get('selected_site')->si_company;
      $customer = Customer::select('id', 'cust_no', 'cust_name', 'cust_type', 'cust_addr',
                                    'cust_telp', 'cust_wa', 'cust_email')
                            ->active()
                            ->get();
      $asset = Asset::select('id', 'inv_transno', 'inv_name')
                      ->whereIn('inv_company', [$selected_company])
                      ->whereIn('inv_status', ['ONHAND', 'RSV'])
                      ->get();

      return view('transaction.selling.edit', compact('sell', 'selected_company', 'customer', 'asset'));
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
        'sell_transdate' => 'required',
        'sell_cust' => 'required',
        'sell_desc' => 'required|max:255',
        'selldtl_desc.*' => 'nullable|max:255',
        'hidden_sell_price.*' => 'required|numeric|min:1'
      ]);

      try {
        //code...
        DB::beginTransaction();
        $qtyCount = count($request->selldtl_transno);
        $amountDepPrice = array_sum($request->selldtl_asset_inv_price);
        $amountSellPrice = array_sum($request->hidden_sell_price);

        $sell = Selling::with('detail')->find($id);
        $sell->update([
          'sell_transdate' => $request->sell_transdate,
          'sell_cust_id' => $request->sell_cust,
          'sell_desc' => $request->sell_desc,
          'sell_cust_name' => $request->cust_name,
          'sell_cust_no' => $request->cust_no,
          'sell_cust_addr' => $request->cust_addr,
          'sell_cust_telp' => $request->cust_telp,
          'sell_cust_wa' => $request->cust_wa,
          'sell_cust_email' => $request->cust_email,
          'sell_qty_item' => $qtyCount,
          'sell_total_price' => $amountSellPrice,
          'sell_amount_dep_price' => $amountDepPrice,
        ]);

        // hapus detail
        $sell->detail->each->delete();

        $detail = array();
        for ($i=0; $i < count($request->selldtl_transno) ; $i++) { 
          # code...
          $asset = Asset::whereIn('inv_transno', [$request->selldtl_transno[$i]])
                        ->whereIn('inv_status', ['ONHAND', 'RSV'])
                        ->first();

          array_push(
            $detail,
            array(
              'selldtl_asset_id' => $asset->id,
              'selldtl_id' => $sell->id,
              'selldtl_transno' => $request->selldtl_transno[$i],
              'selldtl_transdate' => $sell->sell_transdate,
              'selldtl_asset_name' => $request->selldtl_asset_name[$i],
              'selldtl_acc_dep' => $asset->inv_accumulate_dep,
              'selldtl_price' => $request->hidden_sell_price[$i] == null ? 0 : $request->hidden_sell_price[$i],
              'selldtl_dep_price' => $request->selldtl_asset_inv_price[$i],
              'selldtl_desc' => $request->selldtl_desc[$i],
              'selldtl_status' => $sell->sell_status,
              'selldtl_order' => $i+1,
              'created_at' => Carbon::now(),
              'updated_at' => Carbon::now(),
            )
          );
        }

        SellingDetail::insert($detail);

        DB::commit();
      } catch (\Throwable $th) {
        //throw $th;
        dd($th);
        DB::rollback();
        $request->session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Gagal Update Selling!'));
        return redirect()->back();
      }

      $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Berhasil mengubah Selling!'));
      return redirect()->route('selling.index');
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
        // search
        $search = Asset::with('site.company', 'history')
                        ->whereIn('inv_transno', [$request->search])
                        ->whereIn('inv_company', [$request->company])
                        ->where('inv_status', 'ONHAND')
                        ->first();

        if (!$search) {
          session()->flash('notification', array('type' => 'error','title' => 'Gagal!', 'msg' => 'No. Asset tidak ditemukan!'));
          return redirect()->back();
        }

        Log::info('result search : '. json_encode($search));
        return response()->json(['res' => true, 'result' => $search]);
    }

    public function accept($id)
    {
      try {
        DB::beginTransaction();

        $sell = Selling::with('detail')->find($id);

        // update sell_mstr
        $sell->update([
          'sell_status' => 'SELL',
          'sell_approver_name' => Auth::user()->usr_name,
          'sell_Approver_nik' => Auth::user()->usr_nik,
        ]);
        
        foreach ($sell->detail as $key => $dtl) {
          # sell_dtl update
          $dtl->update([
            'selldtl_status' => $sell->sell_status,
          ]);

          $asset = Asset::with('history')->whereIn('inv_transno', [$dtl->selldtl_transno])
                        ->first();

          #inv_mastr update
          $asset->update([
            'inv_status' => $sell->sell_status, // RSV, ONHAND, SELL
          ]);

          #inv_hist create
          $history = InvHist::create([
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

          // create journal selling
          $date = Carbon::parse($sell->sell_transdate);
          $category = Category::find($asset->inv_category);
          $params = GeneralParam::select('param_sales_profit', 'param_sales_loss', 'param_expense_loss', 
                                        'param_asset_transaction', 'param_cash')->first();
          // total pendapatan
          $amountIncome = ($dtl->selldtl_price - $asset->inv_current_price);
          //detail
          $detail = [];
          if ($dtl->selldtl_price > $asset->inv_current_price) {
            # selling (profit)
            array_push(
              $detail,
              array(
                'jld_type' => 'DEBIT',
                'jld_periode' => $date->format('Ym'),
                'jld_site' => $asset->inv_site,
                'jld_account' => $params->param_cash, // Akun Kas / Bank
                'jld_amount' => $dtl->selldtl_price, // harga jual
                'jld_cc' => '',
                'jld_rmks' => $asset->inv_desc,
              ),
              array(
                'jld_type' => 'DEBIT',
                'jld_periode' => $date->format('Ym'),
                'jld_site' => $asset->inv_site,
                'jld_account' => $category->cat_accumulate_depreciation, // Akun Akumulasi Penyusutan
                'jld_amount' => $dtl->selldtl_acc_dep, // akumulasi penyusutan
                'jld_cc' => '',
                'jld_rmks' => $asset->inv_desc,
              ),
              array(
                'jld_type' => 'KREDIT',
                'jld_periode' => $date->format('Ym'),
                'jld_site' => $asset->inv_site,
                'jld_account' => $category->cat_asset, // Akun Asset
                'jld_amount' => $asset->inv_price, // asset
                'jld_cc' => '',
                'jld_rmks' => $asset->inv_desc,
              ),
              array(
                'jld_type' => 'KREDIT',
                'jld_periode' => $date->format('Ym'),
                'jld_site' => $asset->inv_site,
                'jld_account' => $params->param_sales_profit, // Akun Laba Penjualan
                'jld_amount' => $amountIncome, // total laba
                'jld_cc' => '',
                'jld_rmks' => $asset->inv_desc,
              )
            );
          } else {
            # selling (loss)
            array_push(
              $detail,
              array(
                'jld_type' => 'DEBIT',
                'jld_periode' => $date->format('Ym'),
                'jld_site' => $asset->inv_site,
                'jld_account' => $params->param_cash, // Akun Kas / Bank
                'jld_amount' => $dtl->selldtl_price, // harga jual
                'jld_cc' => '',
                'jld_rmks' => $asset->inv_desc,
              ),
              array(
                'jld_type' => 'DEBIT',
                'jld_periode' => $date->format('Ym'),
                'jld_site' => $asset->inv_site,
                'jld_account' => $category->cat_accumulate_depreciation, // Akun Akumulasi Penyusutan
                'jld_amount' => $dtl->selldtl_acc_dep, // akumulasi penyusutan
                'jld_cc' => '',
                'jld_rmks' => $asset->inv_desc,
              ),
              array(
                'jld_type' => 'DEBIT',
                'jld_periode' => $date->format('Ym'),
                'jld_site' => $asset->inv_site,
                'jld_account' => $params->param_sales_loss, // Akun Rugi Penjualan
                'jld_amount' => $amountIncome, // Nominal Rugi jual
                'jld_cc' => '',
                'jld_rmks' => $asset->inv_desc,
              ),
              array(
                'jld_type' => 'KREDIT',
                'jld_periode' => $date->format('Ym'),
                'jld_site' => $asset->inv_site,
                'jld_account' => $category->cat_asset, // Akun Asset
                'jld_amount' => $asset->inv_price, // asset
                'jld_cc' => '',
                'jld_rmks' => $asset->inv_desc,
              )
            );
          }
        }
        
        if ($detail) {
          # header
          $payload['jl_period'] = $date->format('Ym');
          $payload['jl_eff_date'] = $date->format('Y-m-d');
          $payload['hcompany'] = 'H001';
          $payload['jl_company'] = $asset->inv_company;
          $payload['jl_site'] = $asset->inv_site;
          $payload['jl_ref'] = $asset->inv_doc_ref;
          $apyload['rmks'] = 'Penjualan Asset';
          $payload['jl_rowttl'] = count($detail);
          $payload['user'] = Auth::user()->usr_nik;
          $payload['detail'] = $detail;
          
          Log::info('payload journal : ', $payload);
          // $payloadJournal = new JournalController();
          // $payloadJournal->journalAsset($payload);
        }

        DB::commit();
      } catch (Throwable $th) {
        //throw $th;
        dd($th);
        DB::rollback();
        session()->flash('notification', array('type' => 'error', 'title' => 'Gagal!', 'msg' => 'Terjadi kesalahan, harap coba kembali.'));
        return redirect()->back();
      }
      session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Data berhasil diposting'));
      return redirect()->route('selling.index');
    }

    public function toggleState($id)
    {
      // dd($id);
      $sell = Selling::with('detail')->find($id);
      // sell_status == 'CANCEL'
      $sell->sell_status = 'CANCEL'; // RSV, ONHAND, CANCEL
      $sell->save();

      foreach ($sell->detail as $key => $dtl) {
        # selldtl_status == 'CANCEL'
        $dtl->update([
          'selldtl_status' => $sell->sell_status,
        ]);

        $asset = Asset::whereIn('inv_transno', [$dtl->selldtl_transno])
                        ->first();
        // dd($asset);
        $asset->update([
          'inv_status' => 'ONHAND',
        ]);

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

      return array('res' => true);
    }
}

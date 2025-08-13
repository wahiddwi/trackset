<?php

namespace App\Http\Controllers;

use PDF;
use Throwable;
use Carbon\Carbon;
use App\Models\Asset;
use App\Models\Module;
use App\Models\Receive;
use App\Models\Transfer;
use Illuminate\Http\Request;
use App\Models\ReceiveDetail;
use App\Models\TransferDetail;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Http\Controllers\InventoryController;

class ReceiveController extends Controller
{
    public function __construct() {
        $this->middleware(['permission']);
        $this->middleware(['permission:create'])->only(['create', 'store']);
        $this->middleware(['permission:print'])->only(['print']);
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {

            $model = Transfer::with('siteFrom', 'locFrom', 'locTo', 'siteTo', 'user', 'detail')
                    // ->where('trf_status', 'TRF')
                    ->isSuper();


            return DataTables::of($model)
                    ->editColumn('rcv.site_from', function (Transfer $transfer) {
                        // dd($transfer->site->si_name);
                        return $transfer->siteFrom->si_name;
                    })
                    ->editColumn('rcv.loc_from', function (Transfer $transfer) {
                        return $transfer->locFrom->loc_name;
                    })
                    ->editColumn('rcv.site_to', function (Transfer $transfer) {
                        // dd($transfer->siteTo);
                        return $transfer->siteTo->si_name;
                    })
                    ->editColumn('rcv.loc_to', function (Transfer $transfer) {
                        return $transfer->locTo->loc_name;
                    })
                    ->editColumn('rcv.pic_to', function (Transfer $transfer) {
                        if ($transfer->user != null && $transfer->trf_pic == $transfer->user->usr_nik) {
                            return $transfer->user->usr_name;
                        } else {
                            return $transfer->siteTo->si_name;
                        }
                    })
                    ->toJson();
        }

        $modules = Module::isSuper()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
        $count = Transfer::isSuper()->count();
        $menuId = $request->attributes->get('menuId');

        return view('transaction.receive.list', compact('modules', 'count', 'menuId'));
    }

    public function accept(Request $request, $id)
    {
        // $data = $request->all();
        try {
            DB::beginTransaction();

            $date = Carbon::now()->toArray();
            $currentDate = $date['month'] . '/' . $date['year'];
            $receiveId = IdGenerator::generate(['table' => 'transfer_detail', 'field' => 'receive_id', 'length' => 16, 'prefix' => 'RCV/' . $currentDate . '/', 'reset_on_prefix_change' => true]);

            // update transfer detail
            $details = TransferDetail::with('transfer', 'asset')->find($id);

            $details->receive_id = $receiveId;
            $details->trf_detail_status = 'ONHAND';
            $details->trf_detail_pic = $details->transfer->trf_pic_to;
            $details->received_by = Auth::user()->usr_nik;
            $details->save();

            // update asset
            $details->asset->update([
                'inv_status' => 'ONHAND'
            ]);

            // update transfer
            $details->transfer->update([
                'trf_status' => 'ONHAND',
            ]);

            // create inventory history
            $history['invhist_transno'] = $details->asset->inv_transno;
            $history['invhist_inv'] = $details->asset->id;
            $history['invhist_category'] = $details->asset->inv_category;
            $history['invhist_site'] = $details->asset->inv_site;
            $history['invhist_loc'] = $details->asset->inv_loc;
            $history['invhist_depreciation'] = $details->asset->inv_depreciation;
            $history['invhist_name'] = $details->asset->inv_name;
            $history['invhist_pic'] = $details->asset->inv_pic;
            $history['invhist_obtaindate'] = $details->asset->inv_obtaindate;
            $history['invhist_price'] = $details->asset->inv_price;
            $history['invhist_status'] = 'ONHAND';
            $history['invhist_desc'] = $details->asset->inv_desc;
            $history['invhist_sn'] = $details->asset->inv_sn;
            $history['invhist_doc_ref'] = $details->asset->inv_doc_ref;

            $inv = new InventoryController();
            $inv->store($history);

            DB::commit();
        } catch (Throwable $th) {
            //throw $th;
            DB::rollback();

            dd($th->getMessage());

            return redirect()->back();
        }

        return redirect()->back();
    }

    public function detail($id)
    {
        $details = Transfer::with('siteFrom', 'locFrom', 'siteTo', 'locTo', 'detail', 'user')->find($id);

        return response()->json($details);
    }

    public function print($id)
    {
        // dd($id);
        // $transfer = Transfer::with('detail', 'siteFrom', 'siteTo', 'locFrom','locTo', 'userFrom', 'userTo')->find($id);
        $detail = TransferDetail::with('transfer.siteFrom', 'transfer.siteTo', 'transfer.locFrom', 'transfer.locTo', 'transfer.userFrom', 'transfer.userTo', 'received', 'createdBy')->find($id);
        $print = PDF::loadview('transaction.receive.print', compact('detail'));
        return $print->stream();

    }

    public function printAll($id)
    {
        $receive = Transfer::with('detail', 'siteFrom', 'siteTo', 'locFrom','locTo', 'userFrom', 'userTo')->find($id);
        $print = PDF::loadview('transaction.receive.print-all', compact('receive'));
        return $print->stream();
    }
}

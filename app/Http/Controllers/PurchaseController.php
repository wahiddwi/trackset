<?php

namespace App\Http\Controllers;

use PDF;
use Throwable;
use Carbon\Carbon;
use App\Models\Site;
use App\Models\User;
use App\Models\Module;
use App\Models\Category;
use App\Models\Location;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Models\PurchaseDetail;
use App\Exports\PurchaseExport;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Http\Controllers\InventoryController;
use App\Models\CategoryDepreciation as Depreciation;

class PurchaseController extends Controller
{
    public function __construct() {
        $this->middleware(['permission']);
        $this->middleware(['permission:create'])->only(['create', 'store']);
        $this->middleware(['permission:update'])->only(['edit', 'update']);
        $this->middleware(['permission:print'])->only(['print']);
        $this->middleware(['permission:post'])->only(['post']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $model = Purchase::with('site', 'location', 'user')->isSuper();


            return DataTables::of($model)
                    ->editColumn('purchase.si_name', function (Purchase $purchase) {
                        return $purchase->site->si_name;
                    })
                    ->editColumn('purchase.loc_name', function (Purchase $purchase) {
                        return $purchase->location->loc_name;
                    })
                    ->editColumn('purchase.pic', function (Purchase $purchase) {
                        if ($purchase->user != null && $purchase->purchase_pic == $purchase->user->usr_nik) {
                            return $purchase->user->usr_name;
                        } else {
                            return $purchase->site->si_name;
                        }
                    })
                    ->toJson();
        }

        $modules = Module::isSuper()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
        $count = Purchase::isSuper()->count();
        $menuId = $request->attributes->get('menuId');
        $purchase = Purchase::with('detail', 'site', 'location', 'user')->where('purchase_status', 'POSTING')->get();

        return view('transaction.purchase.list', compact('modules', 'count', 'menuId', 'purchase'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sites = Site::select('si_site', 'si_name', 'si_active')->where('si_active', true)->get();
        $users = User::select('usr_nik', 'usr_name', 'usr_status')->where('usr_status', true)->get();
        $categories = Category::active()->get();
        $depreciations = Depreciation::active()->get();

        return view('transaction.purchase.create', compact('sites', 'users', 'categories', 'depreciations'));
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
            'purchase_date' => 'required',
            'purchase_site' => 'required',
            'purchase_loc' => 'required',
            'pic_type' => 'required',
            'purchase_pic' => 'required',
            'purchase_detail_name' => 'required',
            'purchase_detail_type' => 'required',
            'purchase_detail_dep' => 'required',
            'purchase_detail_price' => 'required'
        ]);

        // dd($request->all());

        try {
            DB::beginTransaction();
            $date = Carbon::now()->toArray();
            $currentDate = $date['day'] . ':' . $date['month'];
            $purchaseId = IdGenerator::generate(['table' => 'purchase', 'field' => 'purchase_id', 'length' => 20, 'prefix' => 'PUR-' . $request->purchase_site . '-' . $currentDate . '-']);

            $purchase = new Purchase;
            $purchase->purchase_id = $purchaseId;
            $purchase->purchase_date = $request->purchase_date;
            $purchase->purchase_site = $request->purchase_site;
            $purchase->purchase_loc = $request->purchase_loc;
            $purchase->pic_type = $request->pic_type;
            $purchase->purchase_pic = $request->purchase_pic;
            $purchase->purchase_status = 'DRAFT';
            $purchase->purchase_desc = $request->purchase_desc;
            $purchase->save();

            foreach ($request->purchase_detail_name as $key => $values) {
                $addDetails['purchase_code'] = $purchase->id;
                $addDetails['purchase_detail_type'] = $request->purchase_detail_type[$key];
                $addDetails['purchase_detail_dep'] = $request->purchase_detail_dep[$key];
                $addDetails['purchase_detail_price'] = $request->purchase_detail_price[$key];
                $addDetails['purchase_detail_name'] = $request->purchase_detail_name[$key];
                $addDetails['purchase_detail_ref'] = $request->purchase_detail_ref[$key];
                $addDetails['purchase_detail_status'] = 'ON HAND';
                $addDetails['purchase_detail_account'] = $request->purchase_detail_account[$key];

                PurchaseDetail::create($addDetails);
            }

            DB::commit();

            $request->session()->flash('notification', array('type' => 'success','title' => 'Berhasil', 'msg' => 'Purchase berhasil di tambahkan!'));

            return redirect()->route('purchase.index');

        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Purchase::with('site', 'location', 'user', 'detail.category')->find($id);

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $purchase = Purchase::with('detail', 'user', 'site')->find($id);
        $sites = Site::select('si_site', 'si_name', 'si_active')->where('si_active', true)->get();
        $users = User::select('usr_nik', 'usr_name', 'usr_status')->where('usr_status', true)->get();
        $locations = Location::select('loc_site', 'loc_name', 'loc_active', 'id')->where('loc_active', true)->get();
        $categories = Category::active()->get();
        $depreciations = Depreciation::active()->get();

        return view('transaction.purchase.edit', compact('purchase', 'sites', 'users', 'categories', 'locations', 'depreciations'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'purchase_date' => 'required',
    //         'purchase_site' => 'required',
    //         'purchase_loc' => 'required',
    //         'pic_type' => 'required',
    //         'purchase_pic' => 'required',
    //         'purchase_detail_name' => 'required',
    //         'purchase_detail_type' => 'required',
    //         'purchase_detail_dep' => 'required',
    //         'purchase_detail_price' => 'required'
    //     ]);


    //     try {
    //         //code...
    //         DB::beginTransaction();

    //         $purchase = Purchase::find($id);
    //         $purchase->purchase_date = $request->purchase_date;
    //         $purchase->purchase_site = $request->purchase_site;
    //         $purchase->purchase_loc = $request->purchase_loc;
    //         $purchase->pic_type = $request->pic_type;
    //         $purchase->purchase_pic = $request->purchase_pic;
    //         $purchase->purchase_status = 'DRAFT';
    //         $purchase->purchase_desc = $request->purchase_desc;
    //         $purchase->save();

    //         foreach ($request->purchase_detail_name as $key => $values) {
    //             $addDetails['purchase_detail_type'] = $request->purchase_detail_type[$key];
    //             $addDetails['purchase_detail_dep'] = $request->purchase_detail_dep[$key];
    //             $addDetails['purchase_detail_price'] = $request->purchase_detail_price[$key];
    //             $addDetails['purchase_detail_name'] = $request->purchase_detail_name[$key];
    //             $addDetails['purchase_detail_ref'] = $request->purchase_detail_ref[$key];
    //             $addDetails['purchase_detail_status'] = 'ON HAND';

    //             PurchaseDetail::where('id', $request->id[$key])->update($addDetails);
    //         }

    //         DB::commit();

    //         $request->session()->flash('notification', array('type' => 'success','title' => 'Berhasil', 'msg' => 'Pembelian berhasil diubah!'));

    //         return redirect()->route('purchase.index');

    //     } catch (\Exception $e) {
    //         DB::rollback();

    //         return $e->getMessage();
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     //
    // }

    public function getLocation(Request $request)
    {
        $locations = Location::select('id', 'loc_site', 'loc_name')->where('loc_site', $request->loc_site)->get();
        return response()->json($locations);
    }

    public function acceptPurchase(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $data = Purchase::with('detail')->find($id);
            $data->update([
                'purchase_status' => 'POSTING',
            ]);


            foreach($data->detail as $detail) {
                $addInv['inv_transno'] =  $detail['purchase_detail_id'];
                $addInv['inv_category'] =  $detail['purchase_detail_type'];
                $addInv['inv_location'] = $data->purchase_loc;
                $addInv['inv_depreciation'] = $detail['purchase_detail_dep'];
                $addInv['inv_name'] =  $detail['purchase_detail_name'];
                $addInv['inv_obtaindate'] =  $data->purchase_date;
                $addInv['inv_pic'] =  $data->purchase_pic;
                $addInv['inv_pic_type'] =  $data->pic_type;
                $addInv['inv_price'] = $detail['purchase_detail_price'];
                $addInv['inv_description'] =  $data->purchase_desc;
                $addInv['inv_status'] =  $data->purchase_status;
                $addInv['inv_account'] =  $detail['purchase_detail_account'];

                $addInv['invhist_site'] =  $data->purchase_site;
                $addInv['invhist_status'] =  $detail['purchase_detail_status'];
                $addInv['invhist_desc'] =  $data->purchase_desc;

                $inventory = new InventoryController();
                $inventory->store($addInv);
            }

                DB::commit();

                $request->session()->flash('notification', array('type' => 'success','title' => 'Berhasil', 'msg' => 'Purchase berhasil di tambahkan!'));

                return redirect()->route('purchase.index');

            } catch (Throwable $th) {
                DB::rollback();
                return $th->getMessage();
        }
    }

    public function rejectPurchase($id)
    {
        $data = Purchase::with('detail')->find($id);
        $data->update([
            'purchase_status' => 'REJECTED',
        ]);

        return response()->json($data);
    }

    public function disablePurchase($id)
    {
        $purchase = Purchase::find($id);
        $purchase->purchase_status = "DISABLED";
        $purchase->save();

        return response()->json($purchase);
    }

    public function barcodeItem($id)
    {
        $detail = PurchaseDetail::find($id);
        $customPaper = array(0,0,141.12, 56.16);
        $print = PDF::loadView('transaction.purchase.barcode', compact('detail'))->setPaper($customPaper);
        return $print->stream();
    }

    public function purchaseDetailPrint($id)
    {
        $purchase = Purchase::with('site', 'location', 'user', 'detail.user')->find($id);
        $print = PDF::loadView('transaction.purchase.print-detail', compact('purchase'));
        return $print->stream();
    }

    public function purchasePrint()
    {
        $purchase = Purchase::with('site', 'location', 'user', 'detail.user')->where('purchase_status', 'POSTING')->get();
        $print = PDF::loadView('transaction.purchase.print', compact('purchase'));
        return $print->stream();
    }

    public function downloadPDF()
    {
        $purchase = Purchase::with('site', 'location', 'user', 'detail.category')->where('purchase_status', 'POSTING')->get();
        // dd($purchase->count());
        $pdf = PDF::loadView('transaction.purchase.print', compact('purchase'));
        return $pdf->download('purchase-'. Carbon::now()->timestamp .'.pdf');
    }

    public function exportCSV()
    {
        // return (new PurchaseExport)->download('purchase-'. Carbon::now()->timestamp .'.xlsx');
        return (new PurchaseExport)->download('purchase-'. Carbon::now()->timestamp .'.xlsx');
    }

    public function updateMultiple(Request $request, $id)
    {

        try {
            DB::beginTransaction();

                    $purchase = Purchase::with('detail')->find($id);

                    PurchaseDetail::where('purchase_code', $id)->delete();

                    $data = $request->all();

                    $purchase->update([
                        'purchase_date' => $data['purchase_date'],
                        'purchase_site' => $data['purchase_site'],
                        'purchase_loc' => $data['purchase_loc'],
                        'pic_type' => $data['pic_type'],
                        'purchase_pic' => $data['purchase_pic'],
                        'purchase_status' => 'DRAFT',
                        'purchase_desc' => $data['purchase_desc'],
                    ]);

            if ($request->purchase_detail_name) {
                foreach ($request->purchase_detail_name as $key => $value) {
                    // dd($data);
                    $items = array(
                        'purchase_code' => $purchase->id,
                        'purchase_detail_name' => $data['purchase_detail_name'][$key],
                        'purchase_detail_type' => $data['purchase_detail_type'][$key],
                        'purchase_detail_price' => $data['purchase_detail_price'][$key],
                        'purchase_detail_dep' => $data['purchase_detail_dep'][$key],
                        'purchase_detail_ref' => $data['purchase_detail_ref'][$key],
                        'purchase_detail_status' => 'ON HAND',
                        // 'purchase_detail_account' => $data['purchase_detail_account'][$key],
                    );
                    PurchaseDetail::create($items);
                }
            }

            DB::commit();

        } catch (Throwable $th) {
            //throw $th;
            DB::rollback();

            return redirect()->back()->with('error', $th->getMessage());
        }

        return redirect()->route('purchase.index');
    }
}

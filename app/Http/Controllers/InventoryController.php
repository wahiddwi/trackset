<?php

namespace App\Http\Controllers;

use PDF;
use Exception;
use Throwable;
use Carbon\Carbon;
use App\Models\Site;
use App\Models\User;
use App\Models\Asset;
use App\Models\Module;
use App\Models\InvHist;
use App\Models\Category;
use App\Models\Location;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     private $menuId;
     public function __construct() {
         $this->middleware(['permission']);
         $this->middleware('permission:create')->only(['create', 'store']);
        //  $this->middleware('permission:update')->only(['edit', 'update']);
     }

    public function index(Request $request)
    {
        if ($request->has('search')) {
            $search = InvHist::with('inventory', 'category', 'site', 'user')->where('invhist_transno', 'LIKE', '%'. $request->search .'%')->get();
        } else {
            $search = 'Data Not Found';
        }

        $sites = Site::active()->get();
        $users = User::active()->get();

        $modules = Module::isSuper()->active()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
        // $count = Inventory::isSuper()->count();
        $menuId = $request->attributes->get('menuId');

        return view('transaction.inventory.list', compact('modules', 'menuId', 'sites', 'users', 'search'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($data)
    {
        InvHist::create([
            'invhist_transno' => $data['invhist_transno'],
            'invhist_inv' => $data['invhist_inv'],
            'invhist_category' => $data['invhist_category'],
            'invhist_site' => $data['invhist_site'],
            'invhist_loc' => $data['invhist_loc'],
            'invhist_depreciation' => $data['invhist_depreciation'],
            'invhist_name' => $data['invhist_name'],
            'invhist_pic' => $data['invhist_pic'],
            'invhist_obtaindate' => $data['invhist_obtaindate'],
            'invhist_price' => $data['invhist_price'],
            'invhist_status' => $data['invhist_status'],
            'invhist_desc' => $data['invhist_desc'],
            'invhist_sn' => $data['invhist_sn'],
            'invhist_doc_ref' => $data['invhist_doc_ref'],
            'invhist_merk' => $data['invhist_merk'],
            'invhist_cur_price' => $data['invhist_cur_price'],
            'invhist_dep_periode' => $data['invhist_dep_periode'],
            'invhist_dep_amount' => $data['invhist_dep_amount'],
            'invhist_tag' => $data['invhist_tag'],
            'invhist_name_short' => $data['invhist_name_short'],
            'is_vehicle' => $data['is_vehicle'],
        ]);

        return;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $inventory = Asset::select('id', 'inv_transno', 'inv_category', 'inv_site', 'inv_loc',
                        'inv_depreciation', 'inv_name', 'inv_pic_type', 'inv_pic', 'inv_obtaindate',
                        'inv_price', 'inv_status', 'inv_desc', 'inv_sn', 'inv_doc_ref', 'inv_current_price')
                    ->with('history', 'category.depreciation', 'site',
                            'location', 'history.site', 'history.location', 'history.user')
                    ->find($id);
        $history = InvHist::with('inventory')->where('invhist_inv', $id)->get();

        return view('report.inventory.detail', compact('inventory', 'history'));

    }

    public function search(Request $request)
    {

        $search = $request->search;

        $inventory = Asset::select('id', 'inv_transno', 'inv_category', 'inv_site', 'inv_loc', 'inv_depreciation', 'inv_name', 'inv_pic_type',
                        'inv_pic', 'inv_obtaindate', 'inv_price', 'inv_status', 'inv_desc', 'inv_sn', 'inv_doc_ref', 'created_by', 'updated_by',
                        'updated_at', 'inv_current_price')->with('history', 'category.depreciation', 'site', 'location', 'history.site', 'history.location', 'history.user',
                        'user')->where('inv_transno', $search)
            ->where('inv_status', '!=', 'DRAFT')
            ->first();
        if(!$inventory){
            return response()->json(array('res' => false, 'message' => 'No. Asset Tersebut tidak ditemukan'));
        }

        $history = InvHist::with('inventory')->where('invhist_transno', $search)->get();

        return response()->json(array('res' => true, 'inv' => $inventory, 'hist' => $history));
    }

    public function import_store($import, $count)
    {
        // $import->data->validate([
        //   '*.inv_category' => 'required|exists:categories,id',
        //   '*.inv_site' => 'required|exists:sites:si_site',
        //   '*.inv_loc' => 'required|exists:loc_mstr,id'
        //   '*.inv_name' => 
        // ]);

        $invId = IdGenerator::generate(['table' => 'inv_mstr', 'field' => 'inv_transno', 'length' => 15, 'prefix' => $import->company.'/'. $import->year .'/'. month2roman($import->month) .'/', 'reset_on_prefix_change' => true]);
        $num = getLastDocumentNumber(Asset::class, 'inv_transno', array('inv_obtaindate' => $import->date), 'month', 6);
        // $lastId  = $import->company.'/'. $import->year . '/' . month2roman($import->month) . '/' . str_pad($num, 6, '0', STR_PAD_LEFT);

        // if (Asset::where('inv_transno', $lastId)->exists()) {
        //   # code...
        //   $invId = $import->company.'/'. $import->year . '/' . month2roman($import->month) . '/' . str_pad($num + 1, 6, '0', STR_PAD_LEFT);
        // } else {
        //   # code...
        //   $invId = $import->company.'/'. $import->year . '/' . month2roman($import->month) . '/' . str_pad($num, 6, '0', STR_PAD_LEFT);
        // }


        $insData = array();
        for ($i=0; $i < $count ; $i++) {
          # code...
          // $asset = Asset::create([
          //   'inv_transno' => $invId,
          //   'inv_category' => $import->data[$i]['inv_category'],
          //   'inv_site' => $import->data[$i]['inv_site'], 
          //   'inv_loc' => $import->data[$i]['inv_loc'], 
          //   'inv_depreciation' => $import->data[$i]['inv_depreciation'], 
          //   'inv_name' => $import->data[$i]['inv_name'], 
          //   'inv_name_short' => $import->data[$i]['inv_name_short'], 
          //   'inv_pic_type' => $import->data[$i]['inv_pic_type'], 
          //   'inv_pic' => $import->data[$i]['inv_pic'],
          //   'inv_obtaindate' => $import->data[$i]['inv_obtaindate'], 
          //   'inv_price' => $import->data[$i]['inv_price'],
          //   'inv_status' => $import->status,
          //   'inv_desc' => $import->data[$i]['inv_desc'],
          //   'inv_sn' => $import->data[$i]['inv_sn'],
          //   'inv_doc_ref' => $import->data[$i]['inv_doc_ref'],
          //   'inv_nominal_dep' => $import->data[$i]['inv_nominal_dep'],
          //   'inv_accumulate_dep' => $import->data[$i]['inv_accumulate_dep'],
          //   'inv_end_date' => $import->data[$i]['inv_end_date'],
          //   'inv_merk' => $import->data[$i]['inv_merk'],
          //   'inv_current_price' => $import->data[$i]['inv_current_price'],
          //   'inv_dep_periode' => $import->data[$i]['inv_dep_periode'],
          //   'inv_dep_amount' => $import->data[$i]['inv_dep_amount'],
          //   'inv_company' => $import->data[$i]['inv_company'],
          //   'inv_tag' => $import->data[$i]['inv_tag'],
          // ]);
          array_push(
            $insData,
            array(
              'inv_transno' => $invId,
              'inv_category' => $import->data[$i]['inv_category'],
              'inv_site' => $import->data[$i]['inv_site'], 
              'inv_loc' => $import->data[$i]['inv_loc'], 
              'inv_depreciation' => $import->data[$i]['inv_depreciation'], 
              'inv_name' => $import->data[$i]['inv_name'], 
              'inv_name_short' => $import->data[$i]['inv_name_short'], 
              'inv_pic_type' => $import->data[$i]['inv_pic_type'], 
              'inv_pic' => $import->data[$i]['inv_pic'],
              'inv_obtaindate' => $import->data[$i]['inv_obtaindate'], 
              'inv_price' => $import->data[$i]['inv_price'],
              'inv_status' => $import->status,
              'inv_desc' => $import->data[$i]['inv_desc'],
              'inv_sn' => $import->data[$i]['inv_sn'],
              'inv_doc_ref' => $import->data[$i]['inv_doc_ref'],
              'inv_nominal_dep' => $import->data[$i]['inv_nominal_dep'],
              'inv_accumulate_dep' => $import->data[$i]['inv_accumulate_dep'],
              'inv_end_date' => $import->data[$i]['inv_end_date'],
              'inv_merk' => $import->data[$i]['inv_merk'],
              'inv_current_price' => $import->data[$i]['inv_current_price'],
              'inv_dep_periode' => $import->data[$i]['inv_dep_periode'],
              'inv_dep_amount' => $import->data[$i]['inv_dep_amount'],
              'inv_company' => $import->data[$i]['inv_company'],
              'inv_tag' => $import->data[$i]['inv_tag'],
              'created_by' => Auth::user()->usr_nik,
            )
          );
        }
        Asset::insert($insData);

        return;

    }

}

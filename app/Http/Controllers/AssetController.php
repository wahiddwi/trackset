<?php

namespace App\Http\Controllers;

use PDF;
use Exception;
use Throwable;
use Carbon\Carbon;
use App\Models\Pic;
use App\Models\Tag;
use App\Models\Site;
use App\Models\User;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\Module;
use App\Models\InvHist;
use App\Models\Category;
use App\Models\Location;
use App\Models\TagAsset;
use Milon\Barcode\DNS1D;
use Illuminate\Http\File;
use App\Exports\TagExport;
use App\Exports\MerkExport;
use Illuminate\Support\Str;
use App\Exports\AssetExport;
use App\Exports\ReturnAsset;
use App\Imports\AssetImport;
use App\Models\GeneralParam;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Journal\JournalController;
use App\Models\CategoryDepreciation as Depreciation;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Http\Controllers\Depreciation\DepreciationHistoryController;

class AssetController extends Controller
{
    protected $storage_path;
    public function __construct() {
        $this->middleware(['permission']);
        $this->middleware(['permission:create'])->only(['create', 'store']);
        $this->middleware(['permission:update'])->only(['edit', 'update']);
        $this->middleware(['permission:print'])->only(['print']);
        $this->middleware(['permission:post'])->only(['post']);
        $this->middleware(['permission:delete'])->only(['remove']);

        $this->storage_path = 'assets/';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $model = Asset::with(['site', 'location', 'pic', 'category', 'tag']);

            return DataTables::of($model)
              ->editColumn('inv_site', function (Asset $asset) {
                return optional($asset->site)->si_name;
                })
                ->editColumn('inv_loc', function (Asset $asset) {
                  return optional($asset->location)->loc_name;
                })
                ->editColumn('pic', function (Asset $asset) {
                  if ($asset->pic != null && $asset->inv_pic == $asset->pic->pic_nik) {
                      return optional($asset->pic)->pic_name;
                  } elseif ($asset->inv_pic_type == 'cabang' && $asset->inv_pic == $asset->site->si_site) {
                      return optional($asset->site)->si_name;
                  } else {
                      return '';
                  }
                })
                ->orderColumn('inv_status', function ($query, $order) {
                  $query->orderByRaw("
                    CASE
                      WHEN inv_status = 'DRAFT' THEN 0
                      WHEN inv_status = 'ONHAND' THEN 1
                      WHEN inv_status = 'TRF' THEN 2
                      WHEN inv_status = 'RSV' THEN 3
                      WHEN inv_status = 'CANCEL' THEN 4
                      ELSE 5
                    END $order
                  ");
                })
                ->orderColumn('inv_transno', function ($query, $order) {
                  $query->orderby('created_at', $order);
                })
                ->toJson();
        }

        $modules = Module::isSuper()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
        $count = Asset::count();
        $menuId = $request->attributes->get('menuId');

        return view('master.asset.list', compact('modules', 'count', 'menuId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sites = Site::select('si_site', 'si_name', 'si_active', 'si_company')->where('si_active', true)->get();
        $categories = Category::select('id', 'cat_code', 'cat_name', 'cat_active', 'cat_asset', 'cat_percent', 'cat_depreciation',
                        'cat_accumulate_depreciation', 'cat_depreciation_expense', 'updated_at')
                                ->with('depreciation', 'account_asset', 'account_accumulate_dep',
                                    'account_dep_expense')->active()->get();
        $depreciations = Depreciation::select('id', 'dep_code', 'dep_periode', 'dep_type', 'dep_amount_periode', 'dep_active')
                                        ->active()->get();
        $brand = Brand::select('id', 'brand_name', 'brand_status')->active()->get();
        $tags = Tag::select('id', 'tag_name')->active()->get();

        return view('master.asset.create', compact('categories', 'depreciations', 'brand', 'sites', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationRules = array(
            'inv_obtaindate' => ['required'],
            'inv_site' => ['required'],
            'inv_loc' => ['required'],
            'inv_pic_type' => ['required'],
            'inv_pic' => ['required'],
            'inv_name' => ['required'],
            'inv_category' => ['required'],
            'hidden_price' => ['required'],
            'inv_depreciation' => ['required'],
            'inv_sn' => ['required'],
            'inv_doc_ref' => ['required'],
            'inv_depreciation_value' => ['required'],
            'nominal_depreciation_value' => ['required'],
            'inv_desc' => ['nullable'],
            'inv_merk' => ['required'],
            'inv_tag' => ['required'],
            'inv_name_short' => ['required', 'max:24'],
            'is_vehicle' => 'required',

            'fileUpload.*' => ['nullable', 'file'],
        );


        $validator = Validator::make($request->all(), $validationRules);
        if ($validator->fails()) {
            $request->session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Asset tidak dapat didaftarkan!'));
            return response(back()->withInput()->withErrors($validator));
        }

        $validated = $validator->validated();

        DB::transaction(function () use ($request, $validated) {
            $date = Carbon::parse($validated['inv_obtaindate']);
            $month = $date->format('m');
            $year = $date->format('y');
            // get company
            $company = Site::with('company')->find($validated['inv_site']);

            $num = newGetLastDocumentNumber(Asset::class, 'inv_transno', array('inv_company' => $company->si_company), $date, 'year', 5, 10, 'inv_obtaindate', 'inv_transno');

            $inv_transno = substr($company->si_company, 0, 3) . '/' . $date->format('y') . '/' . month2roman($date->format('m')) . '/' . str_pad($num, 5, '0', STR_PAD_LEFT);

            // calculate nominal depreciation
            $nominal_depreciation = $validated['inv_depreciation_value'] != 0 ? $validated['nominal_depreciation_value'] / $validated['inv_depreciation_value'] : 0;
            // get last day of month
            $lastDayOfMonth = Carbon::parse($validated['inv_obtaindate'])->endOfMonth()->toDateString();

            // asset
            $asset = new Asset;
            $asset->inv_transno = $inv_transno;
            $asset->inv_obtaindate = $validated['inv_obtaindate'];
            $asset->inv_site = $validated['inv_site'];
            $asset->inv_loc = $validated['inv_loc'];
            $asset->inv_pic_type = $validated['inv_pic_type'];
            $asset->inv_pic = $validated['inv_pic'];
            $asset->inv_desc = $validated['inv_desc'];
            $asset->inv_name = $validated['inv_name'];
            $asset->inv_category = $validated['inv_category'];
            $asset->inv_price = $validated['hidden_price'];
            $asset->inv_depreciation = $validated['inv_depreciation'];
            $asset->inv_status = 'DRAFT';
            $asset->inv_sn = $validated['inv_sn'];
            $asset->inv_doc_ref = $validated['inv_doc_ref'];
            $asset->inv_nominal_dep = $nominal_depreciation;
            $asset->inv_accumulate_dep = 0;
            $asset->inv_end_date = $lastDayOfMonth;
            $asset->inv_merk = $validated['inv_merk']; // merk of asset
            $asset->inv_current_price = 0; // harga setelah di depresiasi
            $asset->inv_dep_periode = $validated['inv_depreciation_value'];
            $asset->inv_dep_amount = 0;
            $asset->inv_company = $company->si_company;
            $asset->inv_tag = $validated['inv_tag'];
            $asset->inv_name_short = $validated['inv_name_short'];
            $asset->is_vehicle = $validated['is_vehicle']?true:false;
            $asset->save();

            $storagePath = $this->storage_path . $asset->id;
            if (isset($validated['fileUpload'])) {
                foreach ($validated['fileUpload'] as $file) {
                    Storage::putFile($storagePath, $file);
                }
            }
        });

        $request->session()->flash('notification', array('type' => 'success', 'title' => 'Success.', 'msg' => 'Asset berhasil ditambahkan.'));
        return redirect()->route('asset.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $asset = Asset::select('id', 'inv_transno', 'inv_site', 'inv_loc', 'inv_obtaindate', 'inv_category', 'inv_depreciation', 'inv_name', 'inv_pic_type', 'inv_pic', 'inv_price', 'inv_status',
                        'inv_desc', 'inv_sn', 'inv_doc_ref', 'created_by', 'inv_current_price', 'inv_company', 'inv_merk', 'inv_tag', 'inv_name_short', 'is_vehicle')
                      ->with('category.depreciation', 'site', 'location', 'user', 'merk', 'tag')
                      ->find($id);
                      
        $files = Storage::files($this->storage_path . $id);
        $uploadedFiles = $uploadConfigs = [];
        if ($files) {
          $uploadedFiles = array_map(function ($file) {
            return asset('storage/'. $file);
          }, $files);

          $uploadConfigs = array_map(function ($file) {
            $config = [
              'key' => $file,
              'size' => Storage::size($file),
              'downloadUrl' => asset('storage/' . $file),
              'showRemove' => false,
            ];

            if (pathinfo($file, PATHINFO_EXTENSION) == 'pdf') {
              $config['type'] = 'pdf';
            }
            return $config;
          }, $files);
        }
        if (is_null($asset)) {
          return response(array('res' => false));
        }

        return response()->json([
          'res' => true,
          'asset' => $asset,
          'files' => $uploadedFiles,
          'config' => $uploadConfigs,
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
        $asset = Asset::select('id', 'inv_obtaindate', 'inv_site', 'inv_loc', 'inv_pic_type', 'inv_pic',
                                'inv_name', 'inv_category', 'inv_price', 'inv_depreciation', 'inv_sn', 'inv_doc_ref', 'inv_status', 'inv_desc', 
                                'inv_merk', 'inv_nominal_dep', 'inv_dep_periode', 'inv_company', 'inv_tag', 'inv_name_short', 'is_vehicle')
                        ->with('category.depreciation', 'site', 'user', 'location', 'tag')
                        ->find($id);
        $sites = Site::select('si_site', 'si_name', 'si_active', 'si_company')->where('si_active', true)->get();
        $users = Pic::select('id', 'pic_nik', 'pic_name', 'pic_status')->where('pic_status', true)->get();
        $locations = Location::select('id', 'loc_site', 'loc_name', 'loc_active')->active()->get();
        $categories = Category::select('id', 'cat_name')->active()->get();
        $depreciations = Depreciation::select('id', 'dep_periode', 'dep_type', 'dep_amount_periode', 'dep_active')->active()->get();
        $brands = Brand::select('id', 'brand_name', 'brand_status')->active()->get();
        $tags = Tag::select('id', 'tag_name', 'tag_status')->active()->get();

        if (!$asset) {
            session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Data tidak dapat diakses'));
            return redirect()->back();
        }

        if (($asset->inv_status != 'ONHAND' && $asset->inv_status != 'DRAFT')) {
            session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Asset tidak dapat diedit ('. $asset->inv_status .')'));
            return redirect()->back();
        }

        $files = Storage::files($this->storage_path . $id);
        $uploadedFiles = $uploadConfigs = [];
        if ($files) {
            $uploadedFiles = array_map(function ($file) {
              return asset('storage/' . $file);
            }, $files);

            $uploadConfigs = array_map(function ($file) {
              $config = [
                'key' => $file,
                'size' => Storage::size($file),
                'downloadUrl' => asset('storage/' . $file),
              ];

              if (pathinfo($file, PATHINFO_EXTENSION) == 'pdf') {
                $config['type'] = 'pdf';
              }
              return $config;
            }, $files);
          }

        return view('master.asset.edit', compact('asset', 'sites', 'users', 'locations', 'depreciations', 'categories', 'uploadedFiles', 'uploadConfigs', 'brands', 'tags'));
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
        $validationRules = array(
          'inv_obtaindate' => ['required'],
          'inv_site' => ['required'],
          'inv_loc' => ['required'],
          'inv_pic_type' => ['required'],
          'inv_pic' => ['required'],
          'inv_name' => ['required'],
          'inv_category' => ['required'],
          'inv_price' => ['required'],
          'inv_depreciation' => ['required'],
          'inv_sn' => ['required'],
          'inv_doc_ref' => ['required'],
          'inv_depreciation_value' => ['required'],
          'inv_desc' => ['nullable'],
          'inv_merk' => ['required'],
          'inv_company' => ['required'],
          'inv_tag' => ['required'],
          'inv_name_short' => ['required', 'max:24'],
          'is_vehicle' => ['nullable'],
          'inv_status' => ['required'],
          'hidden_price' => ['required'],
      );

        $validator = Validator::make($request->all(), $validationRules);
        if ($validator->fails()) {
            $request->session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Asset tidak dapat diedit'));
            return response(back()->withInput()->withErrors($validator));
          }

        $validated = $validator->validated();

        DB::transaction(function () use ($request, $validated, $id) {
            // calculate nominal depreciation
            $nominal_depreciation = $validated['inv_depreciation_value'] != 0 ? ($validated['hidden_price'] / $validated['inv_depreciation_value']) : 0;

            // get last day of month
            $lastDayOfMonth = Carbon::parse($validated['inv_obtaindate'])->endOfMonth()->toDateString();
            // get company
            $company = Site::with('company')->find($validated['inv_site']);

            $company = Site::with('company')->find($validated['inv_site']);
            $asset = Asset::find($id);
            $asset->inv_obtaindate = $validated['inv_obtaindate'];
            $asset->inv_site = $validated['inv_site'];
            $asset->inv_loc = $validated['inv_loc'];
            $asset->inv_pic_type = $validated['inv_pic_type'];
            $asset->inv_pic = $validated['inv_pic'];
            $asset->inv_desc = $validated['inv_desc'];
            $asset->inv_name = $validated['inv_name'];
            $asset->inv_category = $validated['inv_category'];
            $asset->inv_price = $validated['hidden_price'];
            $asset->inv_depreciation = $validated['inv_depreciation'];
            $asset->inv_status = $validated['inv_status'];
            $asset->inv_sn = $validated['inv_sn'];
            $asset->inv_doc_ref = $validated['inv_doc_ref'];
            $asset->inv_nominal_dep = $nominal_depreciation;
            $asset->inv_accumulate_dep = 0;
            $asset->inv_end_date = $lastDayOfMonth;
            $asset->inv_merk = $validated['inv_merk']; // merk of asset
            $asset->inv_current_price = 0; // harga setelah di depresiasi
            $asset->inv_dep_periode = $validated['inv_depreciation_value'];
            $asset->inv_dep_amount = 0;
            $asset->inv_company = $company->si_company;
            $asset->inv_tag = $validated['inv_tag'];
            $asset->inv_name_short = $validated['inv_name_short'];
            $asset->is_vehicle = $validated['is_vehicle'] ? true : false;
            $asset->save();

        });

        $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Asset berhasil diubah!'));

        return redirect()->route('asset.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $asset = Asset::find($id);
        $asset->delete();

        return array('res' => true);
    }

    public function getLocation(Request $request)
    {
        $locations = Location::select('id', 'loc_site', 'loc_name')->where('loc_site', $request->loc_site)->get();
        return response()->json($locations);
    }

    public function getType()
    {
        $users = Pic::select('id', 'pic_nik', 'pic_name', 'pic_status')->where('pic_status', true)->get();
        $sites = Site::select('si_site', 'si_name', 'si_active')->where('si_active', true)->get();

        return response()->json([
            'user' => $users,
            'site' => $sites
        ], 200);
    }

    public function getCategory(Request $request)
    {
        $category = Category::select('id', 'cat_name', 'cat_code', 'cat_depreciation', 'cat_percent', 'is_vehicle')->with('depreciation')->where('id', $request->id)->get();

        return response()->json($category);
    }

    public function accept($id)
    {
        try {
            DB::beginTransaction();

            $asset = Asset::find($id);
            if ($asset->inv_status != 'DRAFT') {
                return array('res' => false, 'msg' => 'Status error ('. $asset->inv_status .')');
            }

            // update inv_mstr
            $asset->update([
                'inv_status' => 'ONHAND',
            ]);

            // create inv history
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

            $inventory = new InventoryController();
            $inventory->store($history);

            // create journal
            $date = Carbon::parse($asset->inv_obtaindate);
            $category = Category::find($asset->inv_category);
            $params = GeneralParam::select('param_sales_profit', 'param_sales_loss', 'param_expense_loss', 
                                            'param_asset_transaction', 'param_cash')->first();

            // detail 
            if ($category->cat_depreciation != 1) {
              # code...
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
                  'jld_account' => $params->param_asset_transaction, // Transaksi Aktiva Tetap
                  'jld_amount' => $asset->inv_price*-1,
                  'jld_cc' => '',
                  'jld_rmks' => $asset->inv_desc == null ? '' : $asset->inv_desc,
                )
              );
  
              // header
              $payload['jl_period'] = $date->format('Ym');
              $payload['jl_eff_date'] = $date->format('Y-m-d');
              $payload['jl_hcompany'] = 'H001';
              $payload['jl_company'] = $asset->inv_company;
              $payload['jl_site'] = $asset->inv_site;
              $payload['jl_ref'] = $asset->inv_doc_ref;
              $payload['rmks'] = 'PEROLEHAN ASSET';
              $payload['jl_rowttl'] = count($data);
              $payload['user'] = Auth::user()->usr_nik;
              $payload['detail'] = $data;
  
              // $payloadJournal = new JournalController();
              // $payloadJournal->journalAsset($payload);
            }

            DB::commit();
        } catch (Throwable $th) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan, harap coba beberapa saat.');
        }

        return array('res' => true, 'msg' => 'Success.');
    }

    public function reject($id)
    {
        $asset = Asset::find($id);
        $asset->inv_status = 'REJECTED';
        $asset->save();

        return response()->json($asset);
    }

    public function qr($id)
    {
        $asset = Asset::select('id', 'inv_transno', 'inv_name_short', 'inv_status')->find($id);
        if (!$asset) {
            return 'Asset not found';
        }

        if ($asset->inv_status != 'ONHAND') {
            return 'No Access';
        }
        
        $customPaper = array(0,0,141.12, 56.16);
        $print = PDF::loadView('master.asset.qr', compact('asset'))->setPaper($customPaper);
        return $print->stream();
    }

    public function barcode($id)
    {
      $asset = Asset::select('id', 'inv_transno', 'inv_name_short', 'inv_status')->findOrFail($id);
      $generator = new BarcodeGeneratorSVG();
      $barcodeSVG = $generator->getBarcode($asset->inv_transno, $generator::TYPE_CODE_128, 0.9, 30);
      $customPaper = array(0,0,141.12, 56.16);
      
      $pdf = PDF::loadView('master.asset.barcode', compact('asset', 'barcodeSVG'))
                  ->setPaper($customPaper, 'landscape');
      return $pdf->stream('barcode.pdf');
    }

    public function import(Request $request)
    {
      $request->validate([
        'file' => 'required|file|mimes:xlsx,xls,csv',
      ]);

      try {
        DB::beginTransaction();
        ini_set('max_execution_time', 1000);
        ini_set('memory_limit', '1024M');

        // import data
        $import = new AssetImport;
        Excel::import($import, $request->file('file'));

        $exportAsset = [];
        foreach ($import->data as $key => $value) {
          $date = Carbon::parse($value['tgl_perolehan']);

          $category = Category::with('depreciation')
                              ->where('cat_code', $value['kategori'])
                              ->firstOrFail();

          $site = Site::where('si_site', $value['cabang'])->firstOrFail();

          $loc = Location::where('loc_id', $value['lokasi'])->firstOrFail();

          $short_name = Str::limit($value['nama_barang'], 30);

          if ($category->cat_depreciation != 1) {
            $nominal_depre = $value['harga'] / $category->depreciation->dep_amount_periode;
          } else {
            $nominal_depre = null;
          }

          $trim = substr($value['lokasi'], 0, 3);

          $params = GeneralParam::select('param_sales_profit', 'param_sales_loss',
                                          'param_expense_loss', 'param_asset_transaction',
                                          'param_cash')
                                  ->first();

          $num = newGetLastDocumentNumber(Asset::class, 'inv_transno', array('inv_company' => $site->si_company), $date, 'year', 5, 10, 'inv_obtaindate', 'inv_transno');
          $inv_transno = substr($site->si_company, 0, 3) . '/' . $date->format('y') . '/' . month2roman($date->format('m')) . '/' . str_pad($num, 5, '0', STR_PAD_LEFT);
          
          //create asset
          $asset          = Asset::create([
            'inv_transno'        => $inv_transno,
            'inv_category'       => $category->id,
            'inv_loc'            => $loc->id,
            'inv_site'           => $site->si_site,
            'inv_depreciation'   => $category->depreciation->id,
            'inv_name'           => $value['nama_barang'],
            'inv_name_short'     => $value['short_name'],
            'inv_pic_type'       => $value['tipe_pic'],
            'inv_pic'            => $value['pic'],
            'inv_obtaindate'     => $date->format('Y-m-d'),
            'inv_price'          => $value['harga'],
            'inv_status'         => 'ONHAND',
            'inv_desc'           => $value['keterangan'],
            'inv_sn'             => $value['serial_number'],
            'inv_doc_ref'        => $value['dok_referensi'],
            'inv_nominal_dep'    => $nominal_depre,
            'inv_accumulate_dep' => 0,
            'inv_end_date'       => $date->endofMonth()->format('Y-m-d'),
            'inv_merk'           => $value['brand'],
            'inv_current_price'  => 0,
            'inv_dep_periode'    => $category->depreciation->dep_amount_periode,
            'inv_dep_amount'     => 0,
            'inv_company'        => $site->si_company,
            'inv_tag'            => $value['tag'],
            'is_vehicle'         => $category->is_vehicle ? true : false,
          ]);

          Log::info('Data Asset : ', ['asset' => $asset]);

          // create inv history
          $history        = InvHist::create([
            'invhist_transno'      => $asset->inv_transno,
            'invhist_inv'          => $asset->id,
            'invhist_category'     => $asset->inv_category,
            'invhist_site'         => $asset->inv_site,
            'invhist_loc'          => $asset->inv_loc,
            'invhist_depreciation' => $asset->inv_depreciation,
            'invhist_name'         => $asset->inv_name,
            'invhist_pic'          => $asset->inv_pic,
            'invhist_obtaindate'   => $asset->inv_obtaindate,
            'invhist_price'        => $asset->inv_price,
            'invhist_status'       => $asset->inv_status,
            'invhist_desc'         => $asset->inv_desc,
            'invhist_sn'           => $asset->inv_sn,
            'invhist_doc_ref'      => $asset->inv_doc_ref,
            'invhist_merk'         => $asset->inv_merk,
            'invhist_cur_price'    => $asset->inv_current_price,
            'invhist_dep_periode'  => $asset->inv_dep_periode,
            'invhist_dep_amount'   => $asset->inv_dep_amount,
            'invhist_tag'          => $asset->inv_tag,
            'invhist_name_short'   => $asset->inv_name_short,
            'invhist_company'      => $asset->inv_company,
            'is_vehicle'           => $asset->is_vehicle,
          ]);

          // create journal
          if ($category->cat_depreciation != 1) {
            // detail
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
                // 'jld_usercreate' => Auth::user()->usr_nik,
              ),
              array(
                'jld_type' => 'CREDIT',
                'jld_periode' => $date->format('Ym'),
                'jld_site' => $asset->inv_site,
                'jld_account' => $params->param_asset_transaction, // Akun Transaksi Aktiva Tetap
                'jld_amount' => $asset->inv_price*-1,
                'jld_cc' => '',
                'jld_rmks' => $asset->inv_desc == null ? '' : $asset->inv_desc,
              )
            );

            // header
            $payload['jl_period'] = $date->format('Ym');
            $payload['jl_eff_date'] = $date->format('Y-m-d');
            $payload['jl_hcompany'] = 'H001';
            $payload['jl_company'] = $asset->inv_company;
            $payload['jl_site'] = $asset->inv_site;
            $payload['jl_ref'] = $asset->inv_doc_ref;
            $payload['rmks'] = 'PEROLEHAN ASSET';
            $payload['user'] = Auth::user()->usr_nik;
            $payload['detail'] = $data;

            Log::info('Payload Journal : ', ['journal-payload' => $payload]);

            // $payloadJournal = new JournalController();
            // $payloadJournal->journalAsset($payload);
          }
          
          // adding export
          array_push(
            $exportAsset,
            array(
              'kategori' => $asset->inv_category,
              'cabang' => $asset->inv_site,
              'lokasi' => $asset->inv_loc,
              'nama_barang' => $asset->inv_name,
              'short_name' => $asset->inv_name_short,
              'tipe_pic' => $asset->inv_pic_type,
              'pic' => $asset->inv_pic,
              'tgl_perolehan' => date('m/d/Y H:i:s A', strtotime($asset->inv_obtaindate)),
              'harga' => $asset->inv_price,
              'keterangan' => $asset->inv_desc,
              'serial_number' => $asset->inv_sn,
              'dok_referensi' => $asset->inv_doc_ref,
              'brand' => $asset->inv_merk,
              'tag' => $asset->inv_tag,
              'kode' => $asset->inv_transno,
              'id' => $asset->id,
            )
          );
        }
        DB::commit();

      } catch (ValidationException $e) {
        DB::rollback();
        $failures = $e->failures();
        $fail_msg = '';
        foreach ($failures as $fail) {
          $fail_msg .= 'Baris ' . ($fail->row() - 1) . ': ' . $fail->errors()[0] . '; ';
        }
        
        return response()->json([
          'res' => false,
          'msg' => $fail_msg,
        ]);
      }

      if (empty($exportAsset)) {
        $msg = session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Tidak ada data untuk diexport!'));
        return response(back()->withInput()->withErrors($msg));
      }

      Log::info('Data yang akan dieksport : ', ['export-asset' => $exportAsset]);

      return Excel::download(new ReturnAsset($exportAsset), 'result.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function downloadTemplate()
    {
      return Excel::download(new AssetExport, 'asset.xlsx');
    }

    public function file_upload(Request $request)
    {
      $path = Storage::putFile($this->storage_path . $request->match_id, $request->file('fileUpload'));
      $url = asset('storage/' . $path);
      $config = [
        'key' => $path,
        'size' => Storage::size($path),
        'downloadUrl' => $url,
      ];
      if ($request->file('fileUpload')->extension() == 'pdf') {
        $config['type'] = 'pdf';
      }

      $out = [
        'initialPreview' => [$url],
        'initialPreviewConfig' => [$config],
        'initialPreviewAsData' => true
      ];
      return $out;
    }

    public function file_delete(Request $request)
    {
      Storage::delete($request->key);
      return [];
    }

    public function export_tag()
    {
      return Excel::download(new TagExport, 'tag.xlsx');
    }

    public function export_merk()
    {
      return Excel::download(new MerkExport, 'merk.xlsx');
    }

    public function payloadAsset($data)
    {
      // dd($data);
      $date = Carbon::now()->format('ymd-Hi');
      $export = new ReturnAsset($data);
      
      return Excel::download($export, 'asset-'. $date .'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function remove($id)
    {
        try {
            DB::beginTransaction();

            $asset = Asset::with('history')->find($id);
            // if ($asset->inv_status != 'DRAFT') {
            //     return array('res' => false, 'msg' => 'Status error ('. $asset->inv_status .')');
            // }

            if ($asset->inv_status == 'DRAFT') {
              $asset->update(['inv_status' => 'CANCEL']);
              // dd('DRAFT');
            }

            if ($asset->inv_status == 'ONHAND') {
              # code...
              // dd('ONHAND');
              // $history = InvHist::find($asset->invhist_transno);
              $history = InvHist::whereIn('invhist_transno', [$asset->inv_transno])->first();

              $history->invhist_status = 'CANCEL';
              $history->save();

              $asset->update(['inv_status' => 'CANCEL']);
            }

            // update inv_mstr
            // $asset->update([
            //     'inv_status' => 'CANCEL',
            // ]);

            // // create inv history
            // $history['invhist_transno'] = $asset->inv_transno;
            // $history['invhist_inv'] = $asset->id;
            // $history['invhist_category'] = $asset->inv_category;
            // $history['invhist_site'] = $asset->inv_site;
            // $history['invhist_loc'] = $asset->inv_loc;
            // $history['invhist_depreciation'] = $asset->inv_depreciation;
            // $history['invhist_name'] = $asset->inv_name;
            // $history['invhist_pic'] = $asset->inv_pic;
            // $history['invhist_obtaindate'] = $asset->inv_obtaindate;
            // $history['invhist_price'] = $asset->inv_price;
            // $history['invhist_status'] = 'ONHAND';
            // $history['invhist_desc'] = $asset->inv_desc;
            // $history['invhist_sn'] = $asset->inv_sn;
            // $history['invhist_doc_ref'] = $asset->inv_doc_ref;
            // $history['invhist_merk'] = $asset->inv_merk;
            // $history['invhist_cur_price'] = $asset->inv_current_price;
            // $history['invhist_dep_periode'] = $asset->inv_dep_periode;
            // $history['invhist_dep_amount'] = $asset->inv_dep_amount;
            // $history['invhist_tag'] = $asset->inv_tag;
            // $history['invhist_name_short'] = $asset->inv_name_short;
            // $history['is_vehicle'] = $asset->is_vehicle;

            // $inventory = new InventoryController();
            // $inventory->store($history);

            // // create journal
            // $date = Carbon::parse($asset->inv_obtaindate);
            // $category = Category::find($asset->inv_category);
            // $params = GeneralParam::select('param_sales_profit', 'param_sales_loss', 'param_expense_loss', 
            //                                 'param_asset_transaction', 'param_cash')->first();

            // detail 
            // if ($category->cat_depreciation != 1) {
            //   # code...
            //   $data = [];
            //   array_push(
            //     $data,
            //     array(
            //       'jld_type' => 'DEBIT',
            //       'jld_periode' => $date->format('Ym'),
            //       'jld_site' => $asset->inv_site,
            //       'jld_account' => $category->cat_asset, // Akun Asset Tetap
            //       'jld_amount' => $asset->inv_price,
            //       'jld_cc' => '',
            //       'jld_rmks' => $asset->inv_desc == null ? '' : $asset->inv_desc,
            //     ),
            //     array(
            //       'jld_type' => 'CREDIT',
            //       'jld_periode' => $date->format('Ym'),
            //       'jld_site' => $asset->inv_site,
            //       'jld_account' => $params->param_asset_transaction, // Transaksi Aktiva Tetap
            //       'jld_amount' => $asset->inv_price*-1,
            //       'jld_cc' => '',
            //       'jld_rmks' => $asset->inv_desc == null ? '' : $asset->inv_desc,
            //     )
            //   );
  
            //   // header
            //   $payload['jl_period'] = $date->format('Ym');
            //   $payload['jl_eff_date'] = $date->format('Y-m-d');
            //   $payload['jl_hcompany'] = 'H001';
            //   $payload['jl_company'] = $asset->inv_company;
            //   $payload['jl_site'] = $asset->inv_site;
            //   $payload['jl_ref'] = $asset->inv_doc_ref;
            //   $payload['rmks'] = 'PEROLEHAN ASSET';
            //   $payload['jl_rowttl'] = count($data);
            //   $payload['user'] = Auth::user()->usr_nik;
            //   $payload['detail'] = $data;
  
            //   // $payloadJournal = new JournalController();
            //   // $payloadJournal->journalAsset($payload);
            // }

            DB::commit();
        } catch (Throwable $th) {
            // dd($th);
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan, harap coba beberapa saat.');
        }

        return array('res' => true, 'msg' => 'Success.');
    }

}

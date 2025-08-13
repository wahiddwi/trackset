<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Site;
use App\Models\User;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\Module;
use App\Models\Vendor;
use App\Models\InvHist;
use App\Models\Vehicle;
use App\Models\Category;
use App\Models\Insurance;
use Illuminate\Http\Request;
use App\Models\InsuranceHist;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\InsuranceController;

class VehicleController extends Controller
{
    protected $storage_path;
    protected $storage_insurance_path;
    private $menuId;
    public function __construct() {
        $this->middleware(['permission']);
        $this->middleware('permission:create')->only(['create', 'store']);
        $this->middleware('permission:update')->only(['edit', 'update']);
        $this->middleware('permission:print')->only(['print']);

        $this->storage_path = 'vehicle/';
        $this->storage_insurance_path = 'insurance/';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $model = Vehicle::select('id', 'vehicle_no', 'vehicle_brand', 'vehicle_identityno',
                            'vehicle_engineno', 'vehicle_color', 'vehicle_documentno',
                            'vehicle_capacity', 'updated_at')
                            ->with(['brand:id,brand_name', 'asset']);

            return DataTables::of($model)
                              ->editColumn('vehicle_brand', function (Vehicle $vehicle) {
                                return optional($vehicle->brand)->brand_name;
                              })
                              ->toJson();
        }

        $modules = Module::isSuper()->active()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
        $count = Vehicle::isSuper()->count();
        $menuId = $request->attributes->get('menuId');

        return view('master.vehicle.list', compact('modules', 'count', 'menuId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
      $avail_site = array_keys(Session::get('available_sites')->toArray());
      $company = Site::select('si_site', 'si_name', 'si_company')->with('company')->where('si_site', $avail_site)->first();
      // $company = Site::select('si_site', 'si_name', 'si_company')->with('company')->get();
      $asset = Asset::select('id', 'inv_transno', 'inv_name', 'inv_company', 'inv_site', 'inv_status', 'inv_merk')
                      ->with('vehicle', 'merk')
                      // ->whereIn('inv_company', [$company->si_company])
                      ->where('inv_status', 'ONHAND')
                      ->where('is_vehicle', true)
                      ->doesntHave('vehicle')
                      ->get();

      $menuId = $request->attributes->get('menuId');

      $brand = Brand::select('id', 'brand_name', 'brand_status')->active()->get();
      $vendor = Vendor::select('id', 'vdr_code', 'vdr_name', 'vdr_status')->active()->get();

        return view('master.vehicle.create', compact('asset', 'company', 'brand', 'menuId', 'vendor'));
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
                'vehicle_no' => 'required|string|max:10',
                'hidden_brand' => 'required',
                'vehicle_identityno' => 'required|max:40',
                'vehicle_engineno' => 'required|max:40',
                'vehicle_color' => 'required',
                'vehicle_documentno' => 'required',
                'vehicle_capacity' => 'required|integer',
                'vehicle_desc' => 'nullable|max:255',
                'vehicle_status' => 'nullable',

                // 'fileUpload.*' => 'required|file',
            ]);

            $vehicle = Vehicle::create([
              'vehicle_transno' => $request->hidden_transno,
              'vehicle_no' => $request->vehicle_no,
              'vehicle_name' => $request->hidden_name,
              'vehicle_brand' => $request->hidden_brand,
              'vehicle_documentno' => $request->vehicle_documentno,
              'vehicle_color' => $request->vehicle_color,
              'vehicle_identityno' => $request->vehicle_identityno,
              'vehicle_engineno' => $request->vehicle_engineno,
              'vehicle_capacity' => $request->vehicle_capacity,
              'vehicle_last_km' => $request->vehicle_last_km,
              'vehicle_desc' => $request->vehicle_desc,
              'vehicle_status' => $request->vehicle_status,
              'created_by' => Auth::user()->usr_nik,
              'created_by_name' => Auth::user()->usr_name,
            ]);

            $storagePath = $this->storage_path . $vehicle->id;
            if (isset($request->fileUpload)) {
              foreach ($request->fileUpload as $file) {
                  Storage::putFile($storagePath, $file);
              }
            }
            session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil!', 'msg' => 'Kendaraan Inventaris berhasil didaftarkan.'));
            return redirect()->route('vehicle.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     $vehicle = Vehicle::select('id', 'vehicle_no', 'vehicle_transno', 'vehicle_brand', 'vehicle_identityno',
    //                   'vehicle_engineno', 'vehicle_color', 'vehicle_documentno', 'vehicle_capacity', 'vehicle_desc')
    //                 ->find($id);
    //     $user = User::active()->get();

    //     $files = Storage::files($this->storage_path . $id);
    //     $uploadedFiles = $uploadConfigs = [];
    //     if ($files) {
    //       $uploadedFiles = array_map(function ($file) {
    //         return asset('storage/'. $file);
    //       }, $files);

    //       $uploadConfigs = array_map(function ($file) {
    //         $config = [
    //           'key' => $file,
    //           'size' => Storage::size($file),
    //           'downloadUrl' => asset('storage/' . $file),
    //           'showRemove' => false,
    //         ];

    //         if (pathinfo($file, PATHINFO_EXTENSION) == 'pdf') {
    //           $config['type'] = 'pdf';
    //         }
    //         return $config;
    //       }, $files);
    //     }
    //     if (is_null($vehicle)) {
    //       return response(array('res' => false));
    //     }

    //     return response()->json([
    //       'res' => true,
    //       'data' => $vehicle,
    //       'user' => $user,
    //       'files' => $uploadedFiles,
    //       'config' => $uploadConfigs,
    //     ]);
    // }

    public function show($id)
    {
        $vehicle = Vehicle::select('id', 'vehicle_no', 'vehicle_transno', 'vehicle_brand', 'vehicle_identityno',
                              'vehicle_engineno', 'vehicle_color', 'vehicle_documentno', 'vehicle_capacity', 'vehicle_desc', 'vehicle_name')
                            ->find($id);
        $brand = Brand::select('id', 'brand_name', 'brand_status')->active()->get();
        $vendor = Vendor::select('id', 'vdr_code', 'vdr_name', 'vdr_status')->active()->get();
        $history = InsuranceHist::with('vendor')->where('inshist_vehicle', $id)
                    ->orderBy('created_at', 'DESC')
                    ->get();

        // $user = User::active()->get();

        // file vehicle
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

        // file insurance
        if ($vehicle->insurance) {
          # code...
          $insuranceFiles = Storage::files($this->storage_insurance_path . $vehicle->insurance->id);
          $uploadedInsuranceFiles = $uploadInsuranceConfigs = [];
          if ($insuranceFiles) {
            $uploadedInsuranceFiles = array_map(function ($insuranceFile) {
              return asset('storage/'. $insuranceFile);
            }, $insuranceFiles);
  
            $uploadInsuranceConfigs = array_map(function ($insuranceFile) {
              $insuranceConfig = [
                'key' => $insuranceFile,
                'size' => Storage::size($insuranceFile),
                'downloadUrl' => asset('storage/' . $insuranceFile),
                'showRemove' => false,
              ];
  
              if (pathinfo($insuranceFile, PATHINFO_EXTENSION) == 'pdf') {
                $insuranceConfig['type'] = 'pdf';
              }
              return $insuranceConfig;
            }, $insuranceFiles);
          }
        } else {
          $uploadedInsuranceFiles = null;
          $uploadInsuranceConfigs = null;
        }


        if (is_null($vehicle)) {
          return response(array('res' => false));
        }

        return view('master.vehicle.detail', compact('vehicle', 'brand', 'vendor', 'uploadedFiles', 'uploadConfigs', 'uploadedInsuranceFiles', 'uploadInsuranceConfigs', 'history'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $vehicle = Vehicle::with(['insurance.asset', 'asset'])->find($id);
        $brand = Brand::select('id', 'brand_name', 'brand_status')->active()->get();
        $vendor = Vendor::select('id', 'vdr_code', 'vdr_name', 'vdr_status')->active()->get();
        $history = InsuranceHist::with('vendor')->where('inshist_vehicle', $id)
                    ->orderBy('created_at', 'DESC')
                    ->get();

        // upload vendor
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

        // // upload insurance
        // $filesInsurance = Storage::files($this->storange_insurance_path . $vehicle->insurance->id);
        // $uploadedFilesInsurence = $uploadConfigsInsurance = [];
        // if ($filesInsurance) {
        //   $uploadedFilesInsurence = array_map(function ($fileInsurance) {
        //     return asset('storage/' . $fileInsurance);
        //   }, $filesInsurance);

        //   $uploadConfigsInsurance = array_map(function ($fileInsurance) {
        //     $config = [
        //       'key' => $fileInsurance,
        //       'size' => Storage::size($fileInsurance),
        //       'downloadUrl' => asset('storage/' . $fileInsurance),
        //     ];

        //     if (pathinfo($fileInsurance, PATHINFO_EXTENSION) == 'pdf') {
        //       $config['type'] = 'pdf';
        //     }
        //     return $config;
        //   }, $filesInsurance);
        // }

        return view('master.vehicle.edit', compact('vehicle', 'brand', 'uploadedFiles', 'uploadConfigs', 'vendor', 'history'));
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
        try {
          DB::beginTransaction();

          $request->validate([
            'vehicle_color' => 'required',
            'vehicle_desc' => 'nullable',
            'hist_polisno' => 'required',
            'hidden_hist_covervalue' => 'required',
            'hist_covervalue' => 'required',
            'hist_startdate' => 'required',
            'hist_enddate' => 'required',
            'hist_vehicle' => 'required',
            'hist_premi' => 'required',
            'hidden_hist_premi' => 'required',
            'hist_vendor' => 'required',
            'hist_asset' => 'required',
    
            'fileInsurance.*' => 'required|file',
          ]);
    
          $vehicle = Vehicle::with('insurance')->find($id);
          $vehicle->update([
            'vehicle_color' => $request->vehicle_color,
            'vehicle_desc' => $request->vehicle_desc,
          ]);
    
          $insurance['inshist_asset'] = $request->hist_asset;
          $insurance['inshist_vendor'] = $request->hist_vendor;
          $insurance['inshist_vehicle'] = $request->hist_vehicle;
          $insurance['inshist_polishno'] = $request->hist_polisno;
          $insurance['inshist_startdate'] = $request->hist_startdate;
          $insurance['inshist_enddate'] = $request->hist_enddate;
          $insurance['inshist_cover'] = $request->hidden_hist_covervalue;
          $insurance['inshist_premi'] = $request->hidden_hist_premi;
          $insurance['fileInsurance'] = $request->fileInsurance;
          $insurance['filepath'] = $this->storage_insurance_path;
    
          $history = new InsuranceController();
          $history->store($insurance);

          DB::commit();
        } catch (\Throwable $th) {
          dd($th);
          DB::rollback();
          return redirect()->back()->with('error', 'Terjadi kesalahan, harap coba beberapa saat.');
        }
        $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil!', 'msg' => 'Kendaraan Inventaris berhasil diubah.'));
        return redirect()->route('vehicle.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $vehicle = Vehicle::with('insurance')->find($id);
        $vehicle->insurance->delete();

        session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil!', 'msg' => 'Kendaraan Inventaris berhasil dihapus.'));
        return redirect()->route('vehicle.index');
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $company = $request->company;

        $inventory = Asset::select('id', 'inv_transno', 'inv_category', 'inv_site', 'inv_loc', 'inv_depreciation', 'inv_name', 'inv_pic_type',
                        'inv_pic', 'inv_obtaindate', 'inv_price', 'inv_status', 'inv_desc', 'inv_sn', 'inv_doc_ref', 'created_by', 'updated_by',
                        'updated_at', 'is_vehicle', 'inv_company', 'inv_merk')
                        // ->with('history', 'category', 'category.depreciation', 'site', 'location', 'history.site', 'history.location', 'history.user',
                        // 'user')
                        ->with('vehicle', 'insurance', 'merk')
                        ->where('inv_transno', $search)
                        ->where('inv_status', 'ONHAND')
                        // ->whereIn('inv_company', [$company])
                        ->where('is_vehicle', true)
                        // ->where('is_vehicle', true)
                        // ->whereRelation('category', 'cat_name', 'like', '%'. $type .'%')
                        // ->whereRelation('insurance', 'cat_name', 'like', '%'. $type .'%')
                        ->first();


        if(!$inventory){
            return response()->json(array('res' => false));
        }

        $history = InvHist::with('inventory')->where('invhist_transno', $search)->get();

        return response()->json(array('res' => true, 'inv' => $inventory, 'hist' => $history));
    }

    // public function delete($id)
    // {
    //     $vehicle = Vehicle::find($id);
    //     $insurance = Insurance::where('vehicle_id', $id)->delete();
    //     $vehicle->delete();

    //     session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil!', 'msg' => 'Kendaraan Inventaris berhasil dihapus.'));
    //     return redirect()->route('vehicle.index');
    // }

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
}

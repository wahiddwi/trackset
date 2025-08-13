<?php

namespace App\Http\Controllers;

use PDF;
use Throwable;
use Carbon\Carbon;
use App\Models\Pic;
use App\Models\Tag;
use App\Models\Site;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\InvHist;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Exports\ReportAssetExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Depreciation;

class ReportAssetController extends Controller
{
  protected $storage_path;
  public function __construct(){
    $this->middleware(['permission']);
    $this->middleware(['permission:update'])->only(['edit', 'update']);
    $this->middleware(['permission:delete'])->only(['delete']);

    $this->storage_path = 'assets/';
  }

  public function index()
  {
    $category = Category::select('id', 'cat_code', 'cat_name', 'cat_active', 'cat_asset', 'cat_depreciation', 
                'cat_accumulate_depreciation', 'cat_depreciation_expense', 'is_vehicle')
                ->active()
                ->get();
    $brand = Brand::select('id', 'brand_name', 'brand_status')->active()->get();
    $tag = Tag::select('id', 'tag_name', 'tag_status')->active()->get();
    $pic = Pic::select('id', 'pic_nik', 'pic_name')->active()->get();
    $sites = Site::select('si_site', 'si_name', 'si_active')->where('si_active', true)->get();

    return view('report.asset.list', compact('category', 'brand', 'tag', 'pic', 'sites'));
  }

  public function getPic(Request $request)
  {
    if ($request->type == 'user') {
      $pic = Pic::select('id', 'pic_nik', 'pic_name')->active()->get();
    } else {
      $pic = Site::select('si_site', 'si_name', 'si_active')->active()->get();
    }

    return response()->json(array('data' => $pic), 200);
  }

  public function getLocation(Request $request)
  {
    $locSite = is_array($request->loc_site) ? implode(',', $request->loc_site) : $request->loc_site;
    $loc = Location::select('id', 'loc_id', 'loc_site', 'loc_name')
                    ->whereIn('loc_site', explode(',', $locSite))
                    ->active()
                    ->get();

    return response()->json($loc);
  }

  public function filter(Request $request)
  {
    $query = Asset::select('id', 'inv_transno', 'inv_category', 'inv_site', 'inv_company', 'inv_loc', 'inv_depreciation',
                    'inv_name', 'inv_pic_type', 'inv_pic', 'inv_obtaindate', 'inv_price', 'inv_status', 'inv_desc', 'inv_sn',
                    'inv_doc_ref', 'inv_accumulate_dep', 'inv_nominal_dep', 'inv_end_date', 'inv_current_price', 'inv_dep_periode',
                    'inv_dep_amount', 'inv_tag', 'inv_merk', 'is_vehicle')
                  ->whereNotIn('inv_status', ['DRAFT']);

    if (!empty($request->input('category'))) {
      $query->whereIn('inv_category', $request->input('category'));
    }

    if (!empty($request->input('brand'))) {
      $query->whereIn('inv_merk', $request->input('brand'));
    }

    if (!empty($request->input('tag'))) {
      $query->whereIn('inv_tag', $request->input('tag'));
    }

    if (!empty($request->input('pic'))) {
      if (empty($request->input('type'))) {
        $msg = session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Pilih filter type terlebih dahulu!'));
        return response(back()->withInput()->withErrors($smg));
      }
      $query->whereIn('inv_pic', $request->input('pic'));
    }

    if (!empty($request->input('loc'))) {
      if (empty($request->input('site'))) {
        $msg = session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Pilih filter Cabang terlebih dahulu!'));
        return response(back()->withInput()->withErrors($smg));
      }
      $query->whereIn('inv_loc', $request->input('loc'));
    }

    if (!empty($request->input('date'))) {
      list($start, $end) = explode(" - ", $request->input('date'));

      $startDate = Carbon::parse($start);
      $endDate = Carbon::parse($end);
      
      $query->whereBetween('inv_obtaindate', [$startDate, $endDate]);
    }

    return DataTables::of($query)
            ->addColumn('asset_category', function (Asset $asset) {
              return $asset->category->cat_code .' - '. $asset->category->cat_name;
            })
            ->addColumn('asset_site', function (Asset $asset) {
              return $asset->site->si_site .' - '. $asset->site->si_name;
            })
            ->addColumn('asset_loc', function (Asset $asset) {
              return $asset->location->loc_name;
            })
            ->addColumn('asset_pic', function (Asset $asset) {
              return $asset->pic != null && $asset->pic->pic_nik ? $asset->pic->pic_nik .' - '. $asset->pic->pic_name : $asset->site->si_site .' - '. $asset->site->si_name;
            })
            ->filterColumn('asset_category', function ($query, $keyword) {
              $query->whereHas('category', function ($subQuery) use ($keyword) {
                $subQuery->where('cat_name', 'ilike', "%{$keyword}%");
              });
            })
            ->orderColumn('inv_status', function ($query, $order) {
              $query->orderByRaw("
                CASE
                  WHEN inv_status = 'ONHAND' THEN 0
                  WHEN inv_status = 'DRAFT' THEN 1
                  WHEN inv_status = 'RSV' THEN 2
                  WHEN inv_status = 'DISPOSAL' THEN 3
                  WHEN inv_status = 'SELL' THEN 4
                  WHEN inv_status = 'CEANCEL' THEN 5
                  ELSE 6
                END $order
              ");
            })
            ->orderColumn('inv_obtaindate', function ($query, $order) {
              $query->orderBy('inv_obtaindate', $order);
            })
            ->orderColumn('id', function ($query, $order) {
              $query->orderBy('id', $order);
            })
            ->make(true);
  }

  public function show($id)
  {
    $report = Asset::select('id', 'inv_transno', 'inv_category', 'inv_company', 'inv_site', 'inv_loc', 
                            'inv_depreciation', 'inv_name', 'inv_pic_type', 'inv_pic', 'inv_obtaindate', 
                            'inv_price', 'inv_status', 'inv_desc', 'inv_sn', 'inv_doc_ref', 'inv_accumulate_dep', 
                            'inv_nominal_dep', 'inv_end_date', 'inv_current_price', 'inv_dep_periode', 'inv_dep_amount', 
                            'inv_tag', 'inv_merk', 'inv_name_short', 'is_vehicle')
                    ->with('category', 'site', 'location', 'pic', 'tag', 'merk')
                    ->find($id);

    return array('res' => true, 'report' => $report);
  }

  public function export(Request $request)
  {
    $category = $request->category;
    $brand = $request->brand;
    $tag = $request->tag;
    $pic = $request->pic;
    $loc = $request->loc;
    $date = $request->date;

    ini_set('memory_limit', '1024M');
    return Excel::download(new ReportAssetExport($category, $brand, $tag, $pic, $loc, $date), 'report.xlsx');
  }

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
      // $depreciations = Depreciation::select('id', 'dep_periode', 'dep_type', 'dep_amount_periode', 'dep_active')->active()->get();
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

      return view('report.asset.edit', compact('asset', 'sites', 'users', 'locations', 'categories', 'uploadedFiles', 'uploadConfigs', 'brands', 'tags'));
  }

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
        // 'nominal_depreciation_value' => ['required'],
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

      return redirect()->route('report.index');
  }

  public function delete($id)
  {
    try {
        DB::beginTransaction();

        
        $asset = Asset::with('history')->find($id);
        
        if ($asset->inv_status == 'DRAFT') {
          $asset->update(['inv_status' => 'CANCEL']);
        } else {
          # code...
          $history = InvHist::whereIn('invhist_transno', [$asset->inv_transno])->first();

          $history->invhist_status = 'CANCEL';
          $history->save();

          $asset->update(['inv_status' => 'CANCEL']);
        }

        // if ($asset->inv_status == 'ONHAND') {
        //   $history = InvHist::whereIn('invhist_transno', [$asset->inv_transno])->first();

        //   $history->invhist_status = 'CANCEL';
        //   $history->save();

        //   $asset->update(['inv_status' => 'CANCEL']);
        // }

        DB::commit();
    } catch (Throwable $th) {
        DB::rollback();
        return redirect()->back()->with('error', 'Terjadi kesalahan, harap coba beberapa saat.');
    }

    return array('res' => true, 'msg' => 'Success.');
  }
}

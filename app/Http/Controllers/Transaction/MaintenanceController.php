<?php

namespace App\Http\Controllers\Transaction;

use PDF;
use Exception;
use Throwable;
use Carbon\Carbon;
use App\Models\Asset;
use App\Models\Module;
use App\Models\Vendor;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use App\Models\MaintenanceHist;
use Yajra\DataTables\DataTables;
use App\Models\MaintenanceDetail;
use Illuminate\Support\Facades\DB;
use App\Models\CategoryMaintenance;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MaintenanceController extends Controller
{
  public function __construct() {
    $this->middleware(['permission']);
    $this->middleware(['permission:create'])->only(['create', 'store']);
    $this->middleware(['permission:update'])->only(['edit', 'update', 'toggleState']);
    $this->middleware(['permission:post'])->only(['post']);
    $this->middleware(['permission:print'])->only(['print']);
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
        $query = Maintenance::with('history', 'asset', 'vendor')
                            ->where('main_company', $selected_company);

        return DataTables::of($query)
                          ->editColumn('main_vendor', function(Maintenance $maintenance) {
                            // return $maintenance->vendor->vdr_name;
                            return optional($maintenance->vendor)->vdr_name;
                          })
                          ->orderColumn('main_status', function($query, $order) {
                            $query->orderByRaw("
                              CASE
                                WHEN main_status = 'DRAFT' THEN 1
                                WHEN main_status = 'POST' THEN 2
                                WHEN main_status = 'CANCEL' THEN 3
                              END $order
                            ");
                          })
                          ->orderColumn('main_transdate', function($query, $order) {
                            $query->orderBy('main_transdate', $order);
                          })
                          ->toJson();
      }

      $modules = Module::where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
      $menuId = $request->attributes->get('menuId');
      $count = Maintenance::where('main_company', $selected_company)->count();
      
      return view('transaction.maintenance.list', compact('modules', 'menuId', 'count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $selected_company = Session::get('selected_site')->si_company;
      $asset = Asset::select('id', 'inv_transno', 'inv_name')
                      ->whereIn('inv_company', [$selected_company])
                      ->where('inv_status', 'ONHAND')
                      ->get();
      $vendor = Vendor::select('id', 'vdr_code', 'vdr_name')->active()->get();
      $cat_maintenance = CategoryMaintenance::select('id', 'mtn_type', 'mtn_status')->active()->get();
      return view('transaction.maintenance.create', compact('asset', 'vendor', 'cat_maintenance'));
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
        'main_company' => 'required',
        'main_transdate' => 'required',
        'main_vendor' => 'required',
        'line_count' => 'required',
        'total_pay' => 'required',
        'line.*' => 'required',
        'asset_id.*' => 'required',
        'asset_transno.*' => 'required',
        'asset_name.*' => 'required',
        'asset_site.*' => 'required',
        'remark.*' => 'required',
        'price.*' => 'required',
      ]);

      try { 
        DB::beginTransaction();
        $date = Carbon::parse($request->main_transdate);
        $num = newGetLastDocumentNumber(Maintenance::class, 'main_transno', array('main_company' => $request->main_company), $date, 'year', 5, 14, 'main_transdate', 'main_transno');
        // MTN/GJB/24/B/00001
        $main_transno =  'MTN/'. substr($request->main_company, 0, 3) . '/' . $date->format('y') . '/' . month2roman($date->format('m')) . '/' . str_pad($num, 5, '0', STR_PAD_LEFT);

        $line_count = count($request->line);
        
        // create maintenance
        $maintenance = Maintenance::create([
          'main_transno' => $main_transno,
          'main_transdate' => $request->main_transdate,
          'main_company' => $request->main_company,
          'main_vendor' => $request->main_vendor,
          'main_status' => 'DRAFT', // DRAFT, POST
          'asset_count' => $line_count,
          'main_total_cost' => $request->total_pay,
          'created_by_name' => Auth::user()->usr_name,
        ]);
        
        // create maintenance detail
        for ($i=0; $i < $line_count ; $i++) { 
          $maintenance_counter = MaintenanceHist::where('mainhist_asset_id', $request->asset_id[$i])
                                                ->count();
          MaintenanceDetail::create([
            'maindtl_id' => $maintenance->id,
            'maindtl_transdate' => $request->main_transdate,
            'maindtl_asset_id' => $request->asset_id[$i],
            'maindtl_asset_transno' => $request->asset_transno[$i],
            'maindtl_asset_name' => $request->asset_name[$i],
            'maindtl_company' => $maintenance->main_company,
            'maindtl_site' => $request->asset_site[$i],
            'maindtl_vendor' => $maintenance->main_vendor,
            'maindtl_cost' => $request->price[$i],
            'maindtl_desc' => $request->remark[$i],
            'maindtl_status' => $maintenance->main_status,
            'maindtl_line' => $request->line[$i],
            'maindtl_count' => $maintenance_counter + 1,
            'created_by_name' => $maintenance->created_by_name,
            'maindtl_cat_mtn' => $request->cat_mtn[$i],
            'maindtl_lastdate' => $request->hidden_lastdate[$i] ? $request->hidden_lastdate[$i] : $request->main_transdate,
            'maindtl_mileage' => $request->hidden_mileage[$i],
          ]);
        }
        DB::commit();
      } catch (Throwable $th) {
        Log::error('Error Create Maintenance : ' .$th);
        DB::rollBack();

        $request->session()->flash('notification', array('type' => 'Error!', 'title' => 'Gagal!', 'msg' => 'Terjadi kesalahan, harap coba kembali.'));
        return redirect()->back();
      }

      $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Berhasil membuat pengajuan maintenance.'));
      return redirect()->route('maintenance.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $maintenance = Maintenance::with('detail', 'history', 'company')
                                ->find($id);

      if (is_null($maintenance)) {
        return response(array('res' => false));
      }

      return view('transaction.maintenance.detail', compact('maintenance'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $maintenance = Maintenance::with('detail', 'history')
                                ->find($id);
      $selected_company = Session::get('selected_site')->si_company;
      $asset = Asset::select('id', 'inv_transno', 'inv_name')
                      ->whereIn('inv_company', [$selected_company])
                      ->where('inv_status', 'ONHAND')
                      ->get();
      $vendor = Vendor::select('id', 'vdr_code', 'vdr_name')->active()->get();
      $cat_maintenance = CategoryMaintenance::select('id', 'mtn_type', 'mtn_status')->active()->get();

      foreach ($maintenance->detail as $dtl) {
        $lastdate = MaintenanceDetail::where('maindtl_asset_id', $dtl->maindtl_asset_id)
                    ->where('maindtl_cat_mtn', $dtl->maindtl_cat_mtn)
                    ->where('maindtl_status', 'POST')
                    ->orderByDesc('maindtl_transdate')
                    ->first();

        $dtl->maindtl_lastdate = $lastdate ? Carbon::parse($lastdate->maindtl_lastdate)->format('Y-m-d') : '';
      }

      return view('transaction.maintenance.edit', compact('maintenance', 'asset', 'cat_maintenance', 'vendor'));
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
        'main_company' => 'required',
        'main_transdate' => 'required',
        'main_vendor' => 'required',
        'line_count' => 'required',
        'total_pay' => 'required',
        'line.*' => 'required',
        'asset_id.*' => 'required',
        'asset_transno.*' => 'required',
        'asset_name.*' => 'required',
        'asset_site.*' => 'required',
        'remark.*' => 'required',
        'price.*' => 'required',
      ]);

      try {
        DB::beginTransaction();
        $maintenance = Maintenance::with('detail')->find($id);
        //update maintenance
        $maintenance->update([
          'main_vendor' => $request->main_vendor,
          'main_transdate' => $request->main_transdate,
          'main_total_cost' => $request->total_pay,
          'asset_count' => $request->line_count,
          'main_company' => $request->main_company,
          'updated_by_name' => Auth::user()->usr_name,
        ]);

        $maintenance->detail->each->delete();

        for ($i=0; $i < $request->line_count ; $i++) {
          $maintenance_count = MaintenanceHist::where('mainhist_asset_id', $request->asset_id[$i])
                                                ->count();
          MaintenanceDetail::create([
            'maindtl_id' => $maintenance->id,
            'maindtl_transdate' => $request->main_transdate,
            'maindtl_asset_id' => $request->asset_id[$i],
            'maindtl_asset_transno' => $request->asset_transno[$i],
            'maindtl_asset_name' => $request->asset_name[$i],
            'maindtl_company' => $maintenance->main_company,
            'maindtl_site' => $request->asset_site[$i],
            'maindtl_vendor' => $maintenance->main_vendor,
            'maindtl_cost' => $request->price[$i],
            'maindtl_desc' => $request->remark[$i],
            'maindtl_status' => $maintenance->main_status,
            'maindtl_line' => $request->line[$i],
            'maindtl_count' => $maintenance_count + 1,
            'updated_by_name' => $maintenance->updated_by_name,
            'maindtl_cat_mtn' => $request->cat_mtn[$i],
            'maindtl_lastdate' => $request->hidden_lastdate[$i] ? $request->hidden_lastdate[$i] : $request->main_transdate,
            'maindtl_mileage' => $request->hidden_mileage[$i],
          ]);
        }

        DB::commit();
      } catch (\Throwable $th) {
        Log::error('Error Update Maintenance : ' .$th);
        DB::rollBack();

        $request->session()->flash('notification', array('type' => 'Error!', 'title' => 'Gagal!', 'msg' => 'Terjadi kesalahan, harap coba kembali.'));
        return redirect()->back();
      }
      $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Berhasil memperbarui pengajuan maintenance.'));
      return redirect()->route('maintenance.index');
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
      $search = Asset::with('site.company', 'history')
                      ->whereIn('inv_transno', [$request->transno])
                      ->whereIn('inv_company', [$request->company])
                      ->where('inv_status', 'ONHAND')
                      ->first();

      if (is_null($search)) {
        session()->flash('notification', array('type' => 'error', 'title' => 'Gagal!', 'msg' => 'No. Asset tidak ditemukan!.'));
        return redirect()->back();
      }

      return response()->json([
        'res' => true,
        'result' => $search
      ]);
    }

    public function post($id)
    {
      try {
        DB::beginTransaction();
        $maintenance = Maintenance::with('detail')->find($id);
        // update maintenance
        $maintenance->update([
          'main_status' => 'POST',
          'approver_by' => Auth::user()->usr_nik,
          'approver_by_name' => Auth::user()->usr_name,
        ]);

        for ($i=0; $i < $maintenance->asset_count ; $i++) { 
          # update maintenance detail
          $maintenance->detail[$i]->update([
            'maindtl_status' => $maintenance->main_status,
            'maindtl_approver_by' => $maintenance->approver_by,
            'maindtl_approver_by_name' => $maintenance->approver_by_name,
          ]);

          //create maintenance history
          MaintenanceHist::create([
            'mainhist_main_id' => $maintenance->id,
            'mainhist_transdate' => $maintenance->main_transdate,
            'mainhist_asset_id' => $maintenance->detail[$i]->maindtl_asset_id,
            'mainhist_asset_transno' => $maintenance->detail[$i]->maindtl_asset_transno,
            'mainhist_asset_name' => $maintenance->detail[$i]->maindtl_asset_name,
            'mainhist_company' => $maintenance->main_company,
            'mainhist_site' => $maintenance->detail[$i]->maindtl_site,
            'mainhist_vendor' => $maintenance->main_vendor,
            'mainhist_cost' => $maintenance->detail[$i]->maindtl_cost,
            'mainhist_desc' => $maintenance->detail[$i]->maindtl_desc,
            'mainhist_count' => $maintenance->detail[$i]->maindtl_count,
            'created_by_name' => $maintenance->created_by_name,
            'updated_by_name' => $maintenance->updated_by_name,
            'approver_by' => $maintenance->approver_by,
            'approver_by_name' => $maintenance->approver_by_name,
            'mainhist_cat_mtn' => $maintenance->detail[$i]->maindtl_cat_mtn,
            'mainhist_lastdate' => $maintenance->detail[$i]->maindtl_lastdate,
            'mainhist_mileage' => $maintenance->detail[$i]->maindtl_mileage,
          ]);
        }
        DB::commit();
      } catch (\Throwable $th) {
        Log::error('Error Post Maintenance : ' .$th);
        DB::rollBack();

        return response()->json([
          'res' => false,
          'msg' => 'Terjadi kesalahan, harap coba kembali.'
        ]);
      }
      return response()->json([
        'res' => true,
        'msg' => 'Berhasil POST pengajuan maintenance.'
      ]);
    }

    public function toggleState($id)
    {
      $maintenance = Maintenance::with('detail')->find($id);
      // update maintenance
      $maintenance->update([
        'main_status' => 'CANCEL',
      ]);

      // update maintenance detail
      $maintenance->detail->each->update([
        'maindtl_status' => 'CANCEL',
      ]);

      return response()->json([
        'res' => true,
        'msg' => 'Berhasil CANCEL pengajuan maintenance.'
      ]);
      
    }

    public function history($id)
    {
      $detail = MaintenanceDetail::find($id);
      $history = MaintenanceHist::whereIn('mainhist_asset_transno', [$detail->maindtl_asset_transno])
                                ->orderBy('mainhist_transdate', 'desc')
                                ->get();
      return view('transaction.maintenance.history', compact('history'));
    }

    public function print($id)
    {
      $data = Maintenance::with('detail', 'company', 'vendor')->find($id);

      if (is_null($data)) {
        return 'No Data';
      }

      $print = PDF::loadview('transaction.maintenance.print_v2', compact('data'));
      $print->setPaper(config('constant.pdf.paperDotMatrix'));
      return $print->stream();
    }

    public function getLastMaintenanceDate(Request $request)
    {
      $assetId = $request->asset_id;
      $catMtnId = $request->cat_mtn_id;

      $lastMaintenance = MaintenanceDetail::where('maindtl_asset_id', $assetId)
                                            ->where('maindtl_cat_mtn', $catMtnId)
                                            ->orderByDesc('maindtl_transdate')
                                            ->get()
                                            ->first(function ($record) {
                                              return trim(strtoupper($record->maindtl_status)) == 'POST';
                                            });

      return response()->json([
        'date' => $lastMaintenance ? Carbon::parse($lastMaintenance->maindtl_transdate)->format('Y-m-d') : null
      ]);
    }
}

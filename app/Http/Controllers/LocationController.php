<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Module;
use App\Models\Location;
use App\Exports\LocExport;
use App\Imports\LocImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Maatwebsite\Excel\Validators\ValidationException;

class LocationController extends Controller
{
    public function __construct(){
      $this->middleware(['permission:update'])->only(['edit', 'update', 'toggleState']);
      $this->middleware(['permission:create'])->only(['create', 'store']);
      $this->middleware(['permission:delete'])->only(['delete']);
      $this->middleware(['permission']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
          $model = Location::select('id', 'loc_id', 'loc_site', 'loc_name', 'loc_active', 'updated_at');
          return DataTables::of($model)
                              // ->editColumn('loc.si_name', function (Location $location) {
                              //   return $location->site->si_name;
                              // })
                              ->toJson();
      }


      $modules = Module::active()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
      $count = Location::count();
      $menuId = $request->attributes->get('menuId');

      return view('master.location.list', compact('modules', 'menuId', 'count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $avail_site = array_keys(Session::get('available_sites')->toArray());
        // $site_list = Site::whereIn('si_site', $avail_site)->get();
        $getCompany = Session::get('selected_site')->si_company;
        // $site_list = Site::select('si_site', 'si_name')
        //                   ->whereIn('si_company', [$getCompany])->get();
        $site_list = Site::select('si_site', 'si_name')
                          ->get();



        return response(view('master.location.create', compact('site_list')));
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
            'loc_id' => ['required', 'max:25', 'unique:loc_mstr,loc_id'],
            'site' => ['required'],
            'name' => ['required', 'max:60'],
        );

        $validator = Validator::make($request->all(), $validationRules);

        if($validator->fails()){
            $request->session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Lokasi tidak dapat di tambahkan!'));
            return back()->withInput()->withErrors($validator);
        }

        $validated = $validator->validated();
        Location::create([
            'loc_id'    => $validated['loc_id'],
            'loc_site'  => $validated['site'],
            'loc_name'  => $validated['name'],
        ])->save();

        $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Lokasi berhasil di tambahkan!'));
        return redirect()->route('location.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Location::select('id', 'loc_id', 'loc_site', 'loc_name', 'updated_by', 'created_by')->find($id);
        // $avail_site = array_keys(Session::get('available_sites')->toArray());
        $getCompany = Session::get('selected_site')->si_company;
        $site_list = Site::select('si_site', 'si_name')
                          ->whereIn('si_company', [$getCompany])->get();

        return response(view('master.location.edit', compact('data', 'site_list')));
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
            'site' => ['required'],
            'name' => ['required', 'max:60'],
        );

        $validator = Validator::make($request->all(), $validationRules);

        if($validator->fails()){
            $request->session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Lokasi tidak dapat di edit!'));
            return back()->withInput()->withErrors($validator);
        }

        $validated = $validator->validated();
        Location::find($id)->update([
            'loc_site'   => $validated['site'],
            'loc_name'   => $validated['name'],
        ]);

        $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Lokasi berhasil di edit!'));
        return redirect()->route('location.index');
    }

    public function toggleState($id){
        $location = Location::select('id', 'loc_active')->find($id);
        $location->loc_active = !$location->loc_active;
        $location->save();

        return array('res'=>true);
    }

    public function import(Request $request)
    {
      $request->validate([
        'file' => 'required|mimes:xlsx, xls,csv'
      ]);

      try {
        DB::beginTransaction();

        Excel::import(new LocImport, $request->file('file'));

        DB::commit();
      } catch (ValidationException $e) {
        //throw $th;
        $failures = $e->failures();
        $msg = '';
        foreach ($failures as $fail) {
          $msg .= 'Baris' . ($fail->row() - 1) . ': ' . $fail->errors()[0] . '; ';
        }
        DB::rollback();
        session()->flash('notification', array('type' => 'error', 'title' => 'Error', 'msg' => $msg));
        return redirect()->route('location.index');
      }
      session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil!', 'msg' => 'Lokasi berhasil ditambahkan.'));
      return redirect()->route('location.index');
    }

    public function downloadTemplate()
    {
      return Excel::download(new LocExport, 'loc.xlsx');
    }
}

<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Pic;
use App\Models\Site;
use App\Models\Module;
use App\Exports\PICExport;
use App\Imports\PICImport;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Validators\ValidationException;

class PicController extends Controller
{
  public function __construct(){
    $this->middleware(['permission:update'])->only(['edit', 'update', 'toggleState']);
    $this->middleware(['permission:create'])->only(['create', 'store']);
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
        $model = Pic::select('id', 'pic_nik', 'pic_name', 'pic_status', 'updated_at');
        return DataTables::of($model)
                            ->toJson();
      }

      $modules = Module::active()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
      $count = Pic::count();
      $menuId = $request->attributes->get('menuId');

      return view('master.pic.list', compact('modules', 'menuId', 'count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $avail_site = array_keys(Session::get('available_sites')->toArray());
        $site_list = Site::whereIn('si_site', $avail_site)->get();

        return response(view('master.pic.create', compact('site_list')));
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
          'pic_nik' => 'required|integer|unique:pic,pic_nik',
          'pic_name' => 'required|max:50',
        ]);

        Pic::create([
          'pic_nik' => $request->pic_nik,
          'pic_name' => $request->pic_name,
          'pic_status' => $request->pic_status?true:false,
        ]);

        $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Account berhasil ditambahkan!'));

        return redirect()->route('pic.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $pic = Pic::select('id', 'pic_nik', 'pic_name', 'pic_status')->find($id);

      return view('master.pic.edit', compact('pic'));
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
          'pic_name' => 'required|max:50',
        ]);

        Pic::find($id)->update([
          'pic_name' => $request->pic_name,
        ]);

        $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Pic berhasil diubah!'));
        return redirect()->route('pic.index');
    }

    public function toggleState($id)
    {
      $pic = Pic::select('id', 'pic_status')->find($id);
      $pic->pic_status = !$pic->pic_status;
      $pic->save();

      return array('res' => true);
    }

    public function import(Request $request)
    {
      $request->validate([
        'file' => 'required|mimes:xlsx, xls,csv'
      ]);

      try {
        //code...
        DB::beginTransaction();
        
        Excel::import(new PICImport, $request->file('file'));

        DB::commit();
      } catch (ValidationException $e) {
        //throw $th;
        $failures = $e->failures();
        $msg = '';
        foreach ($failures as $fail) {
          # code...
          $msg .= 'Baris ' . ($fail->row() - 1) . ': ' . $fail->errors()[0] . '; ';
        }
        DB::rollback();
        return redirect()->route('pic.index')->with('notification', array('type' => 'error', 'title' => 'Error', 'msg' => $msg));
      }

      $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil!', 'msg' => 'PIC berhasil ditambahkan.'));
      return redirect()->route('pic.index');
    }

    public function downloadTemplate()
    {
      return Excel::download(new PICExport, 'pic.xlsx');
    }
}

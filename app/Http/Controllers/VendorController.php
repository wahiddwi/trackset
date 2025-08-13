<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class VendorController extends Controller
{
  private $menuId;
  public function __construct() {
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
        $model = Vendor::select('id', 'vdr_code', 'vdr_name', 'vdr_telp', 'vdr_addr', 'vdr_desc', 'vdr_status', 'updated_at');
        return DataTables::of($model)
                      ->orderColumn('vdr_status', function ($query, $order) {
                        $query->orderByRaw("
                          CASE
                            WHEN vdr_status = 'FALSE' THEN 0
                            WHEN vdr_status = 'TRUE' THEN 1
                            ELSE 2
                          END $order
                        ");
                      })
                      ->toJson();
      }

      $modules = Module::isSuper()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
      $count = Vendor::count();
      $menuId = $request->attributes->get('menuId');

      return view('master.agent.list', compact('modules', 'count', 'menuId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       return view('master.agent.create');
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
          'vdr_code' => 'required|string|max:30|unique:vendor_mstr',
          'vdr_name' => 'required|string',
          'vdr_telp' => 'required|max:15',
          'vdr_addr' => 'required|max:255',
          'vdr_desc' => 'nullable|max:255',
        ]);

        Vendor::create([
          'vdr_code' => $request->vdr_code,
          'vdr_name' => $request->vdr_name,
          'vdr_telp' => $request->vdr_telp,
          'vdr_addr' => $request->vdr_addr,
          'vdr_status' => $request->vdr_status ? true : false,
          'vdr_desc' => $request->vdr_desc,
        ]);

        $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Vendor berhasil ditambahkan!'));

        return redirect()->route('agent.index');
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
      $vendor = Vendor::find($id);

      return view('master.agent.edit', compact('vendor'));
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
        'vdr_name' => 'required|string|max:30',
        'vdr_telp' => 'required|max:15',
        'vdr_addr' => 'required|max:255',
        'vdr_desc' => 'nullable|max:255',
      ]);

      $vendor = Vendor::find($id);

      $vendor->update([
        'vdr_name' => $request->vdr_name,
        'vdr_telp' => $request->vdr_telp,
        'vdr_addr' => $request->vdr_addr,
        'vdr_desc' => $request->vdr_desc,
      ]);

      $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Vendor berhasil diubah!'));
      return redirect()->route('agent.index');
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

    public function toggleState($id)
    {
      $vendor = Vendor::find($id);
      $vendor->vdr_status = !$vendor->vdr_status;
      $vendor->save();

      return array('res' => true);
    }
}

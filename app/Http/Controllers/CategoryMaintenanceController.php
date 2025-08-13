<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\CategoryMaintenance;
use App\Rules\UniqueNameCaseInsensitive;

class CategoryMaintenanceController extends Controller
{
  public function __construct() {
    $this->middleware(['permission']);
    $this->middleware('permission:create')->only(['create', 'store']);
    $this->middleware('permission:update')->only(['edit', 'update', 'toggleState']);
  }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
          $model = CategoryMaintenance::select('id', 'mtn_type', 'mtn_desc', 'mtn_status');

          return DataTables::of($model)
                            ->orderColumn('mtn_status', function ($query, $order) {
                              $query->orderByRaw("
                                CASE
                                  WHEN mtn_status = 'ACTIVE' THEN 0
                                  WHEN mtn_status = 'INACTIVE' THEN 1
                                  ELSE 2
                                END $order
                              ");
                            })
                            ->toJson();
        }

        $modules = Module::where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
        $count = CategoryMaintenance::count();
        $menuId = $request->attributes->get('menuId');

        return view('master.category_maintenance.list', compact('modules', 'menuId', 'count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return view('master.category_maintenance.create');
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
          'mtn_type' => [
            'required',
            new UniqueNameCaseInsensitive('category_maintenance'),
          ],
          'mtn_desc' => [
            'nullable',
            'max:255',
          ],
        ]);

        CategoryMaintenance::create([
          'mtn_type' => Str::upper($request->mtn_type),
          'mtn_desc' => $request->mtn_desc,
          'mtn_status' => $request->mtn_status ? true : false,
        ]);

        $request->session()->flash('notification', array('type' => 'success', 'title' => 'Success', 'msg' => 'Category Maintenance berhasil disimpan'));

        return redirect()->route('cat_maintenance.index');
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
        $cat_maintenance = CategoryMaintenance::find($id);

        return view('master.category_maintenance.edit', compact('cat_maintenance'));
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
        'mtn_type' => [
          'required',
          new UniqueNameCaseInSensitive('category_maintenance'),
        ],
        'mtn_desc' => [
          'nullable',
          'max:255',
        ],
      ]);

      $cat_maintenance = CategoryMaintenance::find($id);
      $cat_maintenance->mtn_type = Str::upper($request->mtn_type);
      $cat_maintenance->mtn_desc = $request->mtn_desc ? $request->mtn_desc : '';
      $cat_maintenance->save();

      $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Category Maintenance Berhasil diubah'));
      return redirect()->route('cat_maintenance.index');
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
      $cat_maintenance = CategoryMaintenance::find($id);
      $cat_maintenance->mtn_status = !$cat_maintenance->mtn_status;
      $cat_maintenance->save();

      return array('res' => true);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoryDepreciation;
use App\Models\Module;
use Yajra\DataTables\DataTables;

class CategoryDepreciationController extends Controller
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
        $this->middleware('permission:update')->only(['edit', 'update', 'toggleState']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $model = CategoryDepreciation::select('id', 'dep_code', 'dep_periode', 'dep_type', 'dep_amount_periode', 'dep_active',
                        'updated_at')->isSuper();
            return DataTables::of($model)->toJson();
        }


        $modules = Module::isSuper()->active()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
        $count = CategoryDepreciation::isSuper()->count();
        $menuId = $request->attributes->get('menuId');

        return view('master.category_depreciation.list', compact('modules', 'menuId', 'count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('master.category_depreciation.create');
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
            'dep_code' => 'required|string|max:25|unique:category_depreciations,dep_code',
            'dep_periode' => 'required|integer',
            'dep_type' => 'required',
            'dep_amount_periode' => 'required'
        ]);

        CategoryDepreciation::create([
            'dep_code' => $request->dep_code,
            'dep_periode' => $request->dep_periode,
            'dep_type' => $request->dep_type,
            'dep_active' => $request->dep_active ? true : false,
            'dep_amount_periode' => $request->dep_amount_periode
        ]);

        $request->session()->flash('notification', array('type' => 'success','title' => 'Berhasil', 'msg' => 'Kategori Penyusutan berhasil di tambahkan!'));

        return redirect()->route('cat-depreciations.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = CategoryDepreciation::find($id);
        return view('master.category_depreciation.edit', compact('data'));
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
            'dep_periode' => 'required|integer',
            'dep_type' => 'required',
            'dep_amount_periode' => 'required'
        ]);


        $deprecation = CategoryDepreciation::find($id);
        $deprecation->dep_periode = $request->dep_periode;
        $deprecation->dep_type = $request->dep_type;
        $deprecation->dep_amount_periode = $request->dep_amount_periode;
        $deprecation->save();

        $request->session()->flash('notification', array('type' => 'success','title' => 'Berhasil', 'msg' => 'Kategori Penyusutan berhasil diubah!'));

        return redirect()->route('cat-depreciations.index');
    }

    public function toggleState($id)
    {
        $deprecation = CategoryDepreciation::find($id);
        $deprecation->dep_active = !$deprecation->dep_active;
        $deprecation->save();

        return array('res' => true);

    }
}

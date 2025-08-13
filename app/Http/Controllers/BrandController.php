<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Module;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
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
        $model = Brand::select('id', 'brand_name', 'brand_status', 'updated_at');
        return DataTables::of($model)->toJson();
      }

      $modules = Module::isSuper()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
      $count = Brand::count();
      $menuId = $request->attributes->get('menuId');

      return view('master.brand.list', compact('modules', 'count', 'menuId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('master.brand.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $request['brand_name'] = Str::upper($request->brand_name);

      $validator = Validator::make($request->all(), [
          'brand_name' => ['required', 'string', 'unique:brand_mstr']
      ]);

      if ($validator->fails()) {
        $request->session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Brand sudah terdaftar dengan nama tersebut.'));
        return response(back()->withInput()->withErrors($validator));
      }

      $validated = $validator->validated();

      DB::transaction(function () use ($request, $validated) {
        Brand::create([
          'brand_name' => $validated['brand_name'],
          'brand_status' => $request->brand_status?true:false,
        ]);
      });

      $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Brand berhasil ditambahkan'));
      return redirect()->route('brand.index');
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
        $brand = Brand::find($id);

        return view('master.brand.edit', compact('brand'));
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
      $request['brand_name'] = Str::upper($request->brand_name);

      $validator = Validator::make($request->all(), [
          'brand_name' => ['required', 'string', 'unique:brand_mstr']
      ]);

      if ($validator->fails()) {
        $request->session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Brand sudah terdaftar dengan nama tersebut.'));
        return response(back()->withInput()->withErrors($validator));
      }

      $validated = $validator->validated();

      DB::transaction(function () use ($request, $validated, $id) {
        $brand = Brand::find($id);
        $brand->brand_name = $validated['brand_name'];
        $brand->save();
      });

      $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Brand berhasil diubah'));
      return redirect()->route('brand.index');
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
      $brand = Brand::find($id);
      $brand->brand_status = !$brand->brand_status;
      $brand->save();

      return array('res' => true);
    }
}

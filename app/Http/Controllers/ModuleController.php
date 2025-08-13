<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    public function __construct(){
        $this->middleware(['permission:update'])->only(['edit', 'update', 'toggleState']);
        $this->middleware(['permission:create'])->only(['create', 'store']);
        $this->middleware(['permission']);
    }

    public function index(Request $request)
    {
        if($request->ajax()){
            return DataTables::of(Module::query())
            ->toJson();
        }

        $count = Module::count();
        $menuId = $request->attributes->get('menuId');
        return view('master.module.list', compact('count', 'menuId'));
    }

    public function create()
    {
        $modules = Module::active()->get();
        return view('master.module.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $validationRules = array(
            'code' => ['required', 'unique:modules,mod_code'],
            'name' => ['required'],
            'module' => ['required'],
            'order' => ['required'],
        );

        $validator = Validator::make($request->all(), $validationRules);

        if($validator->fails()){
            $request->session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Module tidak dapat di tambahkan!'));
            return back()->withInput()->withErrors($validator);
        }

        $validated = $validator->validated();
        $modules = Module::create([
            'mod_code'       => $validated['code'],
            'mod_name'       => $validated['name'],
            'mod_desc'       => $request->desc,
            'mod_path'       => $validated['module'],
            'mod_icon'       => $request->icon,
            'mod_parent'     => $request->parent,
            'mod_order'      => $validated['order'],
            'mod_superuser'  => $request->isSuperuser ? true : false,
            // 'created_by' => Auth::user()->usr_nik,
            // 'updated_by' => Auth::user()->usr_nik
        ]);

        Permission::create(['name' => $modules['mod_code'] . '_view']);
        Permission::create(['name' => $modules['mod_code'] . '_create']);
        Permission::create(['name' => $modules['mod_code'] . '_update']);
        Permission::create(['name' => $modules['mod_code'] . '_print']);
        Permission::create(['name' => $modules['mod_code'] . '_post']);
        Permission::create(['name' => $modules['mod_code'] . '_delete']);

        $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Module berhasil di tambahkan!'));
        return redirect()->route('modules.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $modules = Module::active()->get();
        $data = Module::find($id);
        return view('master.module.edit', compact('data', 'modules'));
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
            'name' => ['required'],
            'module' => ['required'],
            'order' => ['required'],
        );

        $validator = Validator::make($request->all(), $validationRules);
        if($validator->fails()){
            $request->session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Module tidak dapat di ubah!'));
            return back()->withInput()->withErrors($validator);
        }
        $validated = $validator->validated();
        Module::find($id)->update([
            'mod_name'       => $validated['name'],
            'mod_desc'       => $request->desc,
            'mod_path'       => $validated['module'],
            'mod_icon'       => $request->icon,
            'mod_parent'     => $request->parent,
            'mod_order'      => $validated['order'],
            'mod_superuser'  => $request->isSuperuser ? true : false,
        ]);

        $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Module berhasil di ubah!'));
        return redirect()->route('modules.index');
    }

    public function toggleState($id){
        $module = Module::find($id);
        $module->mod_active = !$module->mod_active;
        $module->save();

        return array('res' => true);
    }
}

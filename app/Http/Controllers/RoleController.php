<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Module;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    private $menuId;
    public function __construct(){
      $this->middleware(['permission:update'])->only(['edit', 'update', 'toggleState']);
      $this->middleware(['permission:create'])->only(['create', 'store']);
      $this->middleware(['permission']);
        // $this->middleware('auth');
        // $uri = Route::getCurrentRoute()->uri();
        // $route = explode('/', $uri)[0];
        // $this->menuId = \App\Models\Module::where('module_name', $route)->active()->firstOrFail()->code;
        // $this->middleware(['permission:'. $this->menuId . '_view']);
        // $this->middleware(['permission:'. $this->menuId . '_create'])->only(['create', 'store']);
        // $this->middleware(['permission:'. $this->menuId . '_update'])->only(['edit', 'update', 'toggle', 'privilegesUpdate']);
    }

    public function index(Request $request){
        if($request->ajax()){
            $model = Role::query()->isSuper();
            return DataTables::of($model)
                ->toJson();
        }

        $modules = Module::isSuper()->active()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
        $count = Role::isSuper()->count();
        $menuId = $request->attributes->get('menuId');
        return view('master.role.list', compact('count', 'modules', 'menuId'));
    }

    public function toggleState($id){
        $role = Role::select('id', 'role_active', 'role_name', 'name')->find($id);
        $role->role_active = !$role->role_active;
        $role->save();

        return array('res' => true);
    }

    public function create(){
        return view('master.role.create');
    }

    public function store(Request $request){

        $validationRules = array(
            'name' => ['required', 'unique:roles,name'],
            'role_name' => ['required'],
        );

        $validator = Validator::make($request->all(), $validationRules);

        if($validator->fails()){
            $request->session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Posisi tidak dapat di tambahkan!'));
            return back()->withInput()->withErrors($validator);
        }

        $validated = $validator->validated();

        Role::create([
            'name'      => $validated['name'],
            'role_name' => $validated['role_name'],
            ])->save();

        $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Posisi berhasil di tambahkan!'));
        return redirect()->route('roles.index');
    }

    public function edit($id){
        $data = Role::select('id', 'name', 'role_active', 'role_name')->find($id);
        return view('master.role.edit', compact('data'));
    }

    public function update(Request $request, $id){
        $validationRules = array(
            'role_name' => ['required'],
        );

        $validator = Validator::make($request->all(), $validationRules);

        if($validator->fails()){
            $request->session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Posisi tidak dapat di tambahkan!'));
            return back()->withInput()->withErrors($validator);
        }

        $validated = $validator->validated();
        $role = Role::find($id);
        $role->role_name = $validated['role_name'];

        $role->save();

        $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Posisi berhasil di edit!'));
        return redirect()->route('roles.index');
    }

    public function getPrivileges($id){
        $role = Role::find($id);
        if($role == null){
            return array('res' => false);
        }
        $privileges = $role->permissions->pluck('name');
        return array('res' => true, 'data' => $privileges);
    }

    // public function privilegesUpdate(Request $request){
    //     $list = $request->except(['role_id', '_token']);
    //     $role = Role::find($request->role_id);

    //     $privileges = array();
    //     foreach ($list as $key => $privilege) {
    //         foreach ($privilege as $prv) {
    //             array_push($privileges, $key . '_' . $prv);
    //         }
    //     }
    //     $role->syncPermissions($privileges);
    //     return true;
    // }

    public function privilegesUpdate(Request $request)
    {
      $role = Role::find($request->role_id);

      if (!$role) {
        return response()->json([
          'res' => false,
          'msg' => 'Role tidak ditemukan',
        ], 404);
      }

      $list = $request->except(['role_id', '_token']);
      $payloadPermissions = array();
      foreach ($list as $key => $privilege) {
        foreach ($privilege as $prv) {
          $payloadPermissions[] = $key . '_' . $prv;
        }
      }

      $validationPermissions = Permission::whereIn('name', $payloadPermissions)->pluck('name')->toArray();
      $role->syncPermissions($validationPermissions);

      return response()->json([
        'res' => true,
        'msg' => 'Privileges berhasil di update',
        'updated_permissions' => $validationPermissions
      ]);
    }
}

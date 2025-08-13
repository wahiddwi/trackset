<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Site;
use App\Models\User;
use App\Models\Company;
use App\Models\SiteUser;
use App\Mail\PasswordReset;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  public function __construct()
  {
    // add for toggle
    $this->middleware(['permission:update'])->only(['edit', 'update', 'privilegesUpdate', 'toggleState']);
    $this->middleware(['permission:create'])->only(['create', 'store']);
    $this->middleware(['permission']);
  }

  public function index(Request $request)
  {
    if ($request->ajax()) {
      $query = User::select('usr_id', 'usr_nik', 'usr_name', 'usr_email', 'role_id', 'usr_status', 'updated_at')->isSuper()->with('role');

      return DataTables::eloquent($query)
        ->editColumn('role.role_name', function ($data) {
          return $data->role->role_name;
        })
        ->make(true);
    }
    $count = User::isSuper()->count();
    $menuId = $request->attributes->get('menuId');
    $groupSites = Site::active()
                    ->select('si_site', 'si_company', 'si_name')
                    ->with('company:co_company,co_name')
                    ->orderBy('si_company')
                    ->orderBy(DB::raw("CASE WHEN si_site ~ '^[A-Za-z]' THEN 0 ELSE 1 END"))
                    ->orderBy('si_site')
                    ->get()
                    ->groupBy('si_company');
    return view('master.user.list', compact('count', 'menuId', 'groupSites'));
  }

  public function toggleState($id)
  {
    $role = User::select('usr_id', 'usr_nik', 'usr_name', 'usr_status')->find($id);
    $role->usr_status = !$role->usr_status;
    $role->save();

    return array('res' => true);
  }

  public function create()
  {
    $roles = Role::select('id', 'role_active', 'role_name')->active()->isSuper()->get();
    return view('master.user.create', compact('roles'));
  }

  public function store(Request $request)
  {
    $validationRules = array(
      'username' => ['required', 'unique:users,usr_nik'],
      'name' => ['required'],
      'role_id' => ['required'],
      'email' => ['required', 'email'],
      'password' => ['required', \Illuminate\Validation\Rules\Password::min(8)->mixedCase()],
      'password_ulang' => ['required', 'same:password'],
    );

    $validator = Validator::make($request->all(), $validationRules);

    if ($validator->fails()) {
      $request->session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'User tidak dapat di tambahkan'));
      return back()->withInput()->withErrors($validator);
    }

    $validated = $validator->validated();

    $user = User::create([
      'usr_nik' => $validated['username'],
      'usr_name' => $validated['name'],
      'role_id' => $validated['role_id'],
      'usr_email' => $validated['email'],
      'password'  => Hash::make($validated['password']),
      // 'usr_password' => Hash::make($password),
    ]);

    $user->syncRoles($request->role_id);

    $user->save();

    $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'User berhasil di tambahkan!'));
    return redirect()->route('users.index');
  }

  public function edit($id)
  {
    $data = User::select('usr_id', 'usr_nik', 'usr_name', 'usr_status', 'role_id', 'usr_email', 'password')->find($id);
    $roles = Role::select('id', 'role_active', 'role_name')->active()->isSuper()->get();
    return view('master.user.edit', compact('data', 'roles'));
  }

  public function update(Request $request, $id)
  {
    $validationRules = array(
      'name' => ['required'],
      'role_id' => ['required'],
      'email' => ['required', 'email']
    );
    if($request->password != null || $request->password_ulang != null){
      $validationRules = Arr::add($validationRules, 'password', ['required', \Illuminate\Validation\Rules\Password::min(8)->mixedCase()]);
      $validationRules = Arr::add($validationRules, 'password_ulang', ['required', 'same:password']);
    }

    $validator = Validator::make($request->all(), $validationRules);

    if ($validator->fails()) {
      $request->session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'User tidak dapat di ubah!'));
      return back()->withInput()->withErrors($validator);
    }

    $validated = $validator->validated();
    $updData = array(
      'usr_name' => $validated['name'],
      'role_id' => $validated['role_id'],
      'usr_email' => $validated['email'],
    );

    $user = User::find($id);
    // if ($request->type == 'reset') {
    //   $password = Str::random(10);
    //   $details = [
    //     'title' => 'Password Reset',
    //     'id' => $user->username,
    //     'pass' => $password,
    //     'url' => url('/'),
    //     'subject' => 'Reset Password RG Portal',
    //     'body' => 'Password anda baru di reset oleh admin, mohon login kembali dengan data berikut:'
    //   ];
    //   Mail::to($validated['email'])->send(new PasswordReset($details));
    //   $updData['password'] = Hash::make($password);
    // }

    $user->update($updData);
    if($request->password != null){
        $user->password = Hash::make($validated['password']);
    }
    $user->syncRoles($validated['role_id']);
    $user->save();

    $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'User berhasil di edit!'));
    return redirect()->route('users.index');
  }

  public function getPrivileges($id)
  {
    $availableSites = User::find($id)->load('site_privileges')->site_privileges;

    $company = $availableSites
      ->groupBy('site.si_company')
      ->map(function ($sitePrivileges) {
        return $sitePrivileges->count();
      });

      $totalCompany = Company::select('co_company')
      ->withCount([
        'sites' => function ($query) {
          $query->active();
        }
      ])
      ->get()->groupBy('co_company');

      foreach ($company as $co => $value) {
        $company[$co] = $totalCompany[$co]->first()['sites_count'] == $value ? true : false;
      }
  
      // dd($availableSites->load('site')->groupBy('si_company'));
      return array('res' => true, 'data' => $availableSites, 'company' => $company);
  }

  public function privilegesUpdate(Request $request)
  {
    $validationRules = array(
      'usr_id' => ['required'],
    );

    $validator = Validator::make($request->all(), $validationRules);
    if ($validator->fails()) {
      return 'failed';
    }
    $user = $validator->validated()['usr_id'];

    SiteUser::where('su_user', $user)->delete();
    $data = array();
    foreach ($request->siteAccess as $site) {
      $def = $site == $request->default[0] ? true : false;
      array_push($data, array('su_user' => $user, 'su_site' => $site, 'su_default' => $def));
    }
    ;

    SiteUser::insert($data);

    return 'success';
  }
}

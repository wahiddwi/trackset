<?php

namespace App\Http\Controllers\Auth;

use App\Models\Site;
use App\Models\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'changePassword']);
    }

    public function username()
    {
        return 'usr_nik';
    }

    protected function authenticated(Request $request) {
        $this->setMenu();
        $this->setAvailableSites();
        return redirect()->intended($this->redirectPath());
    }

    protected function credentials(Request $request)
    {
        //return $request->only($this->username(), 'password');
        return [$this->username() => $request->{$this->username()}, 'password' => $request->password, 'usr_status' => 1];
    }

    public function changePassword(Request $request){
        $user = Auth::user();

        $validationRules = array(
            'old_pass' => [
                'required',

                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('Password lama tidak sesuai');
                    }
                }
            ],
            'new_pass' => ['required', Password::min(8)->mixedCase(), 'different:old_pass'],
            'retype_pass' => ['required', 'same:new_pass'],
        );

        $validator = Validator::make($request->all(), $validationRules);

        if($validator->fails()){
            $request->session()->flash('notification', array('type' => 'error', 'title' => 'Perhatian!', 'msg' => 'Password tidak dapat dirubah.'));
            $request->session()->flash('pass-error', true);
            return back()->withInput()->withErrors($validator);
        }

        $validated = $validator->validated();
        $user->fill([
            'password' => Hash::make($validated['new_pass'])
        ])->save();

        $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Password berhasil dirubah.'));
        return back();
    }

    public function setAvailableSites(){
        $user = Auth::user();
        $available = $user->load('site_privileges')->site_privileges;
        // $availableSites = $available->load('site')->pluck('site')->pluck('si_name', 'si_site');
        $availableSites = $available->load('site')->sortBy('site.si_site')->pluck('site.si_name', 'site.si_site');
        // $default = Site::find($available->where('su_default', true)->first->site->site);
        if (empty($availableSites->toArray())) {
            Auth::logout();
            session()->flush();
            return redirect()->route('login');
        } else {
            $default = $available->where('su_default', true)->first()->site()->first();
            session(['available_sites' => $availableSites, 'selected_site' => $default]);

        }

    }

    public function setMenu()
    {
        $previleges = Auth::user()->getAllPermissions()->pluck('name');
        $prv_list = array();
        foreach ($previleges as $prv) {
            if (substr($prv, -5) == '_view') {
                $prv = strstr($prv,'_view', true);
                array_push($prv_list, $prv);
            }
        }

        $menus = Module::with(['submenu' => function ($query) {
            $query->select('mod_id', 'mod_code', 'mod_name', 'mod_path',
                        'mod_icon', 'mod_parent', 'mod_order');
                    }])
                    ->where('mod_parent', null)
                    ->orderBy('mod_order', 'asc')
                    ->active()
                    ->select('mod_id', 'mod_code', 'mod_name', 'mod_path',
                            'mod_icon', 'mod_parent', 'mod_order')
                    ->get()
                    ->toArray();
            $list = $this->createMenuList($menus, $prv_list);
            session(['sb_menu' => $list]);
    }

    public function createMenuList($menus, $prv, $output = [])
    {
        foreach ($menus as $menu) {
            if (in_array($menu['mod_code'], $prv)) {
                $data = array(
                    'text' => $menu['mod_name'],
                    'icon' => $menu['mod_icon'],
                    'url' => $menu['mod_path'],
                    'id' => 'sb_menu_' . $menu['mod_code'],
                );

                if (count($menu['submenu'])) {
                    $submenu = $this->createMenuList($menu['submenu'], $prv);
                    if (count($submenu)) {
                        $data['submenu'] = $submenu;
                    }
                    $data['url'] = '#';
                }
                array_push($output, $data);
            }
        }
        return $output;
    }
}

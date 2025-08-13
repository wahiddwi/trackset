<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\Session;


class Authenticate extends Middleware
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
        if (!Session::get('available_sites')) {
            session()->flush();
            FacadesAuth::logout();
            return redirect()->route('login');
        }

        if(FacadesAuth::check()) {
            $this->sessionMenu();
        }
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    public function sessionMenu()
    {
        $list = Session::get('sb_menu');
        if (isset($list)) {
            Event::listen(BuildingMenu::class, function (BuildingMenu $event) use ($list) {
                foreach ($list as $ls) {
                    $event->menu->add($ls);
                }
            });
        }
    }

}

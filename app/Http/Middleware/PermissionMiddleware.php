<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class PermissionMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
   * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
   */
  public function handle(Request $request, Closure $next, $permission = 'view', $guard = null)
  {
    $authGuard = app('auth')->guard($guard);
    if ($authGuard->guest()) {
      throw UnauthorizedException::notLoggedIn();
    }

    // if (!is_null($permission)) {
    //   $permissions = is_array($permission)
    //     ? $permission
    //     : explode('|', $permission);
    // }

    // if (is_null($permission)) {
    //   $permission = $request->route()->getName();
    //   $permissions = array($permission);
    // }

    $route = explode('/', $request->route()->uri)[0];
    $menuId = \App\Models\Module::where('mod_path', $route)->firstOrFail()->mod_code;
    $request->attributes->add(['menuId' => $menuId]);
    if($authGuard->user()->can($menuId.'_'.$permission)){
        $request->session()->put('menuId', $menuId);
      return $next($request);
    }

    throw UnauthorizedException::forPermissions([$menuId.'_'.$permission]);
  }
}

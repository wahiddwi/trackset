<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Module;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SiteController extends Controller
{
  private $menuId;
  public function __construct()
  {
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
      $model = Site::select('si_site', 'si_name', 'si_company_site', 'si_company', 'si_active', 'updated_at'
            )->with('company')->orderBy('si_site', 'asc');

      return DataTables::of($model)
        ->editColumn('company.co_name', function (Site $site) {
          return $site->company->co_name;
        })
        ->toJson();
    }

    $count = Site::count();
    $modules = Module::isSuper()->active()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
    // $menuId = $this->menuId;
    $menuId = $request->attributes->get('menuId');
    return response(view('master.site.list', compact('count', 'menuId', 'modules')));
  }

  public function syncSite()
  {
    $apiUrl = config('app.api_url') . '/sync/sites';
    $token = env('API_KEY_INTERNAL');

    $res = Http::withToken($token)->withHeaders([
                  'Content-Type' => 'application/json',
                ])->get($apiUrl);

    if ($res->successful()) {
      # code...
      $getSite = collect($res->json());

      foreach ($getSite['data'] as $key => $site) {
        # code...
        Site::upsert([
          [
            'si_site' => $site['si_site'],
            'si_name' => $site['si_name'],
            'si_company_site' => $site['si_sitecompany'],
            'si_company' => $site['co_company'],
            'si_active' => $site['si_active'],
            'created_by' => $site['si_usercreate'],
            'updated_by' => $site['si_userupdate']
          ]
        ], ['si_site'], ['si_name', 'si_company_site', 'si_company', 
                        'si_active', 'created_by', 'updated_by']);
      }
      session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Site berhasil diupdate!'));
    } else {
      # code...
      Log::error('Sync Site error: ' . $res->body());
      session()->flash('notification', array('type' => 'error', 'title' => 'Gagal', 'msg' => 'Site gagal diupdate!'));
    }
      return redirect()->route('sites.index');
  }
}

<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

class CompanyController extends Controller
{
    private $menuId;
    public function __construct(){
      $this->middleware(['permission']);
    }

    public function index(Request $request)
    {
        if($request->ajax()){
            $model = Company::query();
            return DataTables::of($model)->toJson();
        }

        $count = Company::count();
        // $menuId = $this->menuId;
        $menuId = $request->attributes->get('menuId');
        return response(view('master.company.list', compact('count', 'menuId')));
    }

    public function syncCompany()
    {
      $apiUrl = config('app.api_url') . '/sync/companies';
      $token = env('API_KEY_INTERNAL');

      $res = Http::withToken($token)->withHeaders([
                'Content-Type' => 'application/json',
              ])->get($apiUrl);

      if ($res->successful()) {
        # code...
        $getCompany = collect($res->json());

        foreach ($getCompany['data'] as $key => $co) {
          # code...
          Company::upsert([
            [
              'co_company' => $co['co_company'],
              'co_name' => $co['co_name'],
              'co_active' => $co['co_active'],
              'created_by' => $co['co_usercreate'],
              'updated_by' => $co['co_userupdate']
            ]
          ], ['co_company'], ['co_name', 'co_active', 'created_by', 'updated_by']);
        }
        session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Company berhasil diupdate!'));
      } else {
        # code...
        Log::error('Sync Company error: ' . $res->body());
        session()->flash('notification', array('type' => 'error', 'title' => 'Gagal', 'msg' => 'Company gagal diupdate!'));
      }

      return redirect()->route('companies.index');
    }
}

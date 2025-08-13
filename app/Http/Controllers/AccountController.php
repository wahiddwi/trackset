<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Account;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $menuId;
    public function __construct() {
        $this->middleware(['permission']);
        $this->middleware(['permission:create'])->only(['create', 'store']);
        $this->middleware(['permission:update'])->only(['edit', 'update', 'toggleState']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $model = Account::isSuper();
            return DataTables::of($model)->toJson();
        }

        $modules = Module::isSuper()->active()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
        $count = Account::isSuper()->count();
        $menuId = $request->attributes->get('menuId');

        return view('master.account.list', compact('modules', 'count', 'menuId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('master.account.create');
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
            'coa_account' => 'required|integer|unique:coa,coa_account',
            'coa_name' => 'required',
            'coa_desc' => 'nullable|max:255',
        ]);

        Account::create([
            'coa_account' => $request->coa_account,
            'coa_name' => $request->coa_name,
            'coa_status' => $request->coa_status,
            'coa_desc' => $request->coa_desc,
        ]);

        $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Account berhasil ditambahkan!'));

        return redirect()->route('account.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $account = Account::find($id);

        return view('master.account.edit', compact('account'));
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
            'coa_name' => 'required',
            'coa_desc' => 'nullable|max:255',
        ]);

        $account = Account::find($id);
        // $account->coa_account = $request->coa_account;
        $account->coa_name = $request->coa_name;
        $account->coa_desc = $request->coa_desc;
        $account->save();

        $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Account berhasil diubah!'));

        return redirect()->route('account.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     //
    // }

    public function toggleState($id)
    {
        $account = Account::find($id);
        $account->coa_status = !$account->coa_status;
        $account->save();

        return array('res' => true);
    }

    public function syncAccount()
    {
      // sync account to rgapi
      $apiUrl = config('app.api_url') . '/sync/general-ledger';
      $token = env('API_KEY_INTERNAL');

      $res = Http::withToken($token)->withHeaders([
                    'Content-Type' => 'application/json',
                  ])->get($apiUrl);

      if ($res->successful()) {
        # code...
        $getAccount = collect($res->json());

        foreach ($getAccount['data'] as $keys => $acc) {
          # code...

          Account::upsert([
            [
              'coa_account' => $acc['gl_account'],
              'coa_name' => $acc['gl_desc'],
              'coa_status' => $acc['gl_active']
            ]
          ], ['coa_account'], ['coa_name', 'coa_status']);

        }
        session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Account berhasil diupdate!'));
      } else {
        # code...
        Log::error('Sync Account error: ' . $res->body());
        session()->flash('notification', array('type' => 'error', 'title' => 'Gagal', 'msg' => 'Account gagal diupdate!'));
      }

      return redirect()->route('account.index');
    }
}

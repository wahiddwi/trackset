<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CustomerController extends Controller
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
          $model = Customer::select('id', 'cust_no', 'cust_name', 'cust_addr', 'cust_telp', 'cust_wa', 'cust_email', 'cust_active', 'cust_internal', 'cust_type',
                  'updated_at')->isSuper();
          return DataTables::of($model)->toJson();
        }

        $modules = Module::isSuper()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
        $count = Customer::isSuper()->count();
        $menuId = $request->attributes->get('menuId');

        return view('master.customer.list', compact('modules', 'count', 'menuId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('master.customer.create');
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
          'cust_no' => 'required|string|unique:cust_mstr,cust_no',
          'cust_type' => 'required',
          'cust_name' => 'required|string|max:30',
          'cust_addr' => 'required|string|max:255',
          'cust_telp' => 'required|max:20',
          'cust_wa' => 'nullable|max:20',
          'cust_email' => 'nullable|max:50',
          'cust_active' => 'nullable',
          'cust_internal' => 'nullable',
        ]);

        Customer::create([
          'cust_no' => $request->cust_no,
          'cust_type' => $request->cust_type,
          'cust_name' => $request->cust_name,
          'cust_addr' => $request->cust_addr,
          'cust_telp' => $request->cust_telp,
          'cust_wa' => $request->cust_wa,
          'cust_email' => $request->cust_email,
          'cust_active' => $request->cust_active ? true : false,
          'cust_internal'=> $request->cust_initernal ? true : false,
        ]);

        $request->session()->flash('notofication', array('type' => 'success', 'title' => 'Berhasil!', 'msg' => 'Customer berhasil ditambahkan!'));
        return redirect()->route('customer.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = Customer::find($id);

        return response()->json([
          'res' => true,
          'data' => $customer,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Customer::find($id);

        return view('master.customer.edit', compact('customer'));
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
        'cust_type' => 'required',
        'cust_name' => 'required|string|max:30',
        'cust_addr' => 'required|string|max:255',
        'cust_telp' => 'required|max:20',
        'cust_wa' => 'nullable|max:20',
        'cust_email' => 'nullable|max:50',
        'cust_internal' => 'nullable',
      ]);

      $customer = Customer::find($id);
      $customer->cust_type = $request->cust_type;
      $customer->cust_name = $request->cust_name;
      $customer->cust_addr = $request->cust_addr;
      $customer->cust_telp = $request->cust_telp;
      $customer->cust_wa = $request->cust_wa;
      $customer->cust_email = $request->cust_email;
      $customer->cust_internal = $request->cust_internal ? true : false;
      $customer->save();

      $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil!', 'msg' => 'Data Customer berhasil diubah!'));
      return redirect()->route('customer.index');
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
      $customer = Customer::find($id);
      $customer->cust_active = !$customer->cust_active;
      $customer->save();

      return array('res' => true);
    }
}

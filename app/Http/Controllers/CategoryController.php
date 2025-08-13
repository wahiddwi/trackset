<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Account;
use App\Models\Company;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\CategoryDepreciation as Depreciation;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     private $menuId;
    public function __construct() {
        $this->middleware(['permission']);
        $this->middleware('permission:create')->only(['create', 'store']);
        $this->middleware('permission:update')->only(['edit', 'update', 'toggleState']);
    }
    public function index(Request $request)
    {
        if($request->ajax()){
            $model = Category::select('id', 'cat_code', 'cat_name', 'cat_active', 'cat_asset',
                        'cat_percent', 'cat_depreciation', 'updated_at')->isSuper();
            return DataTables::of($model)->toJson();
        }

        $modules = Module::isSuper()->active()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
        $count = Category::isSuper()->count();
        $menuId = $request->attributes->get('menuId');
        // dd($menuId);
        // return response(view('master.category.list', compact('count', 'menuId')));
        return view('master.category.list', compact('modules', 'count', 'menuId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $account = Account::select('coa_account', 'coa_name', 'coa_status', 'updated_at')->active()->get();
        $depreciation = Depreciation::select('id', 'dep_periode', 'dep_amount_periode', 'dep_active', 'updated_at')->active()->get();

        return view('master.category.create', compact('account', 'depreciation'));
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
            'cat_code' => 'required|string|max:25|unique:categories,cat_code',
            'cat_name' => 'required|string|max:60',
            'cat_asset' => 'required',
            // 'cat_percent' => 'required',
            'cat_depreciation' => 'required',
            'cat_accumulate_depreciation' => 'required',
            'cat_depreciation_expense' => 'required',
            // 'cat_income' => 'required',
            // 'cat_disposal' => 'required',
            'is_vehicle' => 'nullable'
        ]);

        Category::create([
            'cat_code' => $request->cat_code,
            'cat_name' => $request->cat_name,
            'cat_active' => $request->cat_active ? true : false,
            'cat_asset' => $request->cat_asset,
            // 'cat_percent' => $request->cat_percent,
            'cat_depreciation' => $request->cat_depreciation,
            'cat_accumulate_depreciation' => $request->cat_accumulate_depreciation,
            'cat_depreciation_expense' => $request->cat_depreciation_expense,
            // 'cat_income' => $request->cat_income,
            // 'cat_disposal' => $request->cat_disposal,
            'is_vehicle' => $request->is_vehicle ? true : false,
        ]);


        $request->session()->flash('notification', array('type' => 'success','title' => 'Berhasil', 'msg' => 'Category berhasil di tambahkan!'));

        return redirect()->route('categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Category::select('id', 'cat_code', 'cat_name', 'cat_asset', 'cat_depreciation',
                'cat_accumulate_depreciation', 'cat_depreciation_expense', 'updated_at')->with('account_asset', 'account_accumulate_dep',
                'account_dep_expense')->find($id);

        $account = Account::select('coa_account', 'coa_name', 'coa_status')->active()->get();
        $depreciation = Depreciation::select('id', 'dep_code', 'dep_periode', 'dep_type', 'dep_amount_periode')->active()->get();

        return view('master.category.edit', compact('data', 'account', 'depreciation'));
    }

    public function update(Request $request, $id)
    {
        // $request->validate([
        //     'cat_name' => $request->cat_name,
        //     'cat_active' => $request->cat_active ? true : false,
        //     'cat_asset' => $request->cat_asset,
        //     // 'cat_percent' => $request->cat_percent,
        //     'cat_depreciation' => $request->cat_depreciation,
        //     'cat_accumulate_depreciation' => $request->cat_accumulate_depreciation,
        //     'cat_depreciation_expense' => $request->cat_depreciation_expense,
        //     'cat_income' => $request->cat_income
        // ]);

        $request->validate([
            'cat_name' => 'required|string|max:60',
            'cat_asset' => 'required',
            // 'cat_percent' => 'required',
            'cat_depreciation' => 'required',
            'cat_accumulate_depreciation' => 'required',
            'cat_depreciation_expense' => 'required',
            // 'cat_income' => 'required',
            // 'cat_disposal' => 'required',
            'is_vehicle' => 'nullable',
        ]);

        $category = Category::select('id', 'cat_code', 'cat_name', 'cat_asset', 'cat_depreciation',
                    'cat_accumulate_depreciation', 'cat_depreciation_expense')->find($id);
        $category->cat_name = $request->cat_name;
        $category->cat_asset = $request->cat_asset;
        // $category->cat_percent = $request->cat_percent;
        $category->cat_depreciation = $request->cat_depreciation;
        $category->cat_accumulate_depreciation = $request->cat_accumulate_depreciation;
        $category->cat_depreciation_expense = $request->cat_depreciation_expense;
        // $category->cat_income = $request->cat_income;
        // $category->cat_disposal = $request->cat_disposal;

        $category->save();

        return redirect()->route('categories.index');
    }

    public function toggleState($id)
    {
        $category = Category::select('id', 'cat_active')->find($id);
        $category->cat_active = !$category->cat_active;
        $category->save();

        return array('res' => true);

    }
}

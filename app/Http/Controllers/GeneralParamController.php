<?php
namespace App\Http\Controllers;
use App\Models\Module;
use App\Models\Account;
use App\Models\GeneralParam;
use Illuminate\Http\Request;
class GeneralParamController extends Controller
{
  public function __construct(){
    $this->middleware(['permission:update'])->only(['update']);
    $this->middleware(['permission']);
  }
  public function index(Request $request)
  {
    $modules = Module::active()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
    $menuId = $request->attributes->get('menuId');
    $account = Account::select('coa_account', 'coa_name', 'coa_status')->get();
    $param = GeneralParam::find(1);
    return view('general_param.list', compact('modules', 'menuId', 'account', 'param'));
  }
  public function update(Request $request, $id)
  {
    $request->validate([
      'param_sales_profit' => 'required',
      'param_sales_loss' => 'required',
      'param_expense_loss' => 'required',
      'param_asset_transaction' => 'required',
      'param_cash' => 'required',
    ]);

    if (!empty($request->all())) {
      # code...
      GeneralParam::find(1)->update([
        'param_sales_profit' => $request->param_sales_profit,
        'param_sales_loss' => $request->param_sales_loss,
        'param_expense_loss' => $request->param_expense_loss,
        'param_asset_transaction' => $request->param_asset_transaction,
        'param_cash' => $request->param_cash,
      ]);
    }
    return redirect()->back()->with('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Parameter berhasil diupdate!'));
  }
}

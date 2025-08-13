<?php

namespace App\Http\Controllers\Depreciation;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\DepreciationHistory;
use App\Http\Controllers\Controller;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class DepreciationHistoryController extends Controller
{
    public function __construct() {
        $this->middleware(['permission']);
        $this->middleware(['permission:create'])->only(['create', 'store']);
    }

    public function store($data)
    {
      $date = Carbon::now();
      $num = newGetLastDocumentNumber(DepreciationHistory::class, 'dephist_transno', array('dephist_company' => $data['dephist_company']), $date, 'year', 5, 14, 'dephist_transdate', 'dephist_transno');
      $refId =  'DPH/'. substr($request->dephist_company, 0, 3) . '/' . $date->format('y') . '/' . month2roman($date->format('m')) . '/' . str_pad($num, 5, '0', STR_PAD_LEFT);

        DepreciationHistory::create([
            'dephist_transno' => $refId,
            'dephist_asset_transno' => $data['dephist_asset_transno'],
            'dephist_company' => $data['dephist_company'],
            'dephist_site' => $data['dephist_site'],
            'dephist_transdate' => $data['dephist_transdate'],
            'dephist_acc_asset' => $data['dephist_acc_asset'],
            'dephist_acc_accumulate_dep' => $data['dephist_acc_accumulate_dep'],
            'dephist_acc_depreciation_expense' => $data['dephist_acc_depreciation_expense'],
            'dephist_acc_income' => $data['dephist_acc_income'],
            'dephist_acc_disposal' => $data['dephist_acc_disposal'],
            'dephist_price' => $data['dephist_price'],
            'dephist_accumulate_dep' => $data['dephist_accumulate_dep'],
            'dephist_nominal_dep' => $data['dephist_nominal_dep'],
            'dephist_current_price' => $data['dephist_current_price'],
        ]);

        return;
    }
}

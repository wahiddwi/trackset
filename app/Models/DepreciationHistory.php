<?php

namespace App\Models;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DepreciationHistory extends Model
{
    use HasFactory;

    protected $table = 'depreciation_hist';
    protected $fillable = [
        // 'dephist_ref', // custom no. ref
        // 'dephist_asset_id', // id asset
        // 'dephist_nominal_depreciation',
        // // 'dephist_status', // CLOSED or OPEN
        // 'dephist_periode', // JUN 24
        // 'dephist_company', // company code
        // 'dephist_site' // site code

        'dephist_transno',
        'dephist_asset_transno', // inv_transno
        'dephist_company',
        'dephist_site',
        'dephist_transdate',
        'dephist_acc_asset', // akun asset (coa asset)
        'dephist_acc_accumulate_dep', // akun Akumulasi Penyusutan (coa Akumulasi Penyusutan)
        'dephist_acc_depreciation_expense', // akun Beban Penyusutan (coa Beban Penyusutan)
        'dephist_acc_income', // akun Pendapatan (coa Pendapatan)
        'dephist_acc_disposal', // akun Disposal (coa disposal)
        'dephist_price', // harga awal aseet
        'dephist_accumulate_dep', // akumulasi depresiasi
        'dephist_nominal_dep', // nominal depresiasi
        'dephist_current_price', // harga setelah di depresiasi
    ];

    // public function asset()
    // {
    //     return $this->belongsTo(Asset::class, 'dephist_asset_id', 'inv_transno');
    // }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'dephist_asset_transno', 'inv_transno');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'dephist_company', 'co_company');
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'dephist_site', 'si_site');
    }

    public function coa_asset()
    {
        return $this->belongsTo(Account::class, 'dephist_acc_asset', 'coa_account');
    }

    public function coa_accumulate_dep()
    {
        return $this->belongsTo(Account::class, 'dephist_acc_accumulate_dep', 'coa_account');
    }

    public function coa_depreciation_expense()
    {
        return $this->belongsTo(Account::class, 'dephist_acc_depreciation_expense', 'coa_account');
    }

    public function coa_income()
    {
        return $this->belongsTo(Account::class, 'dephist_acc_income', 'coa_account');
    }

    public function coa_disposal()
    {
        return $this->belongsTo(Account::class, 'dephist_acc_disposal', 'coa_account');
    }
}

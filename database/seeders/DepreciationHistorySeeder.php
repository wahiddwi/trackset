<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Company;
use Illuminate\Database\Seeder;
use App\Models\DepreciationHistory;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class DepreciationHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::select('co_company', 'co_name', 'last_dep')->get();
        
        foreach ($companies as $key => $company) {
          # code...
          $date = Carbon::now();
          $refId = IdGenerator::generate(['table' => 'depreciation_hist', 'field' => 'dephist_transno', 'length' => 15, 'prefix' => $company->co_company . '/' . $date->format('y') . '/' . month2roman($date->format('m')) . '/', 'reset_on_prefix_change' => true]);

          DepreciationHistory::create([
              'dephist_transno' => $refId,
              'dephist_asset_transno' => null, // inv_transno
              'dephist_company' => $company->co_company,
              'dephist_site' => null,
              // 'dephist_transdate' => Carbon::parse(Carbon::now())->startOfMonth()->subMonth()->endOfMonth()->toDateString(),
              'dephist_transdate' => $company->last_dep,
              'dephist_acc_asset' => null, // akun asset (coa asset)
              'dephist_acc_accumulate_dep' => null, // akun Akumulasi Penyusutan (coa Akumulasi Penyusutan)
              'dephist_acc_depreciation_expense' => null, // akun Beban Penyusutan (coa Beban Penyusutan)
              'dephist_acc_income' => null, // akun Pendapatan (coa Pendapatan)
              'dephist_acc_disposal' => null, // akun Disposal (coa disposal)
              'dephist_price' => 0, // harga awal aseet
              'dephist_accumulate_dep' => 0, // akumulasi depresiasi
              'dephist_nominal_dep' => 0, // nominal depresiasi
              'dephist_current_price' => 0, // harga setelah di depresiasi
          ]);
        }

    }
}

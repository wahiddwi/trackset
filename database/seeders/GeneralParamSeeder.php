<?php

namespace Database\Seeders;

use App\Models\GeneralParam;
use Illuminate\Database\Seeder;

class GeneralParamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      GeneralParam::create([
        'param_sales_profit' => '320000000',
        'param_sales_loss' => '320000000',
        'param_expense_loss' => '320000000',
        'param_asset_transaction' => '320000000',
        'param_cash' => '320000000',
      ]);
    }
}

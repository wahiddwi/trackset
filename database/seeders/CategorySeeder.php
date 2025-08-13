<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
            'cat_code' => 'CAT-001',
            'cat_name' => 'No Depreciation',
            'cat_active' => true,
            'cat_percent' => null,
            'cat_depreciation' => 1,
            'cat_account' => null, // asset / coa
            'cat_accumulate_depreciation' => null, // akumulasi penyusutan
            'cat_depreciation_expense' => null, // beban penyusutan
            'cat_income' => null, // pendapatan
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}

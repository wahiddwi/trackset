<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoryDepreciation as Depreciation;

class CategoryDepreciationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Depreciation::Create([
            'dep_code' => 'DEP-001',
            'dep_periode' => 0,
            'dep_type' => null,
            'dep_amount_periode' => null,
            'dep_active' => true
        ]);
    }
}

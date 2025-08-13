<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Site;
use Carbon\Carbon;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Site::create([
            'si_site' => 'H01',
            'si_name' => 'Gadai Jadi Berkah',
            'si_company_site' => true,
            'si_company' => 'GJB',
            'si_active' => true,
            'created_by' => 'ADMIN',
            'updated_by' => 'ADMIN',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Site::create([
            'si_site' => '001',
            'si_name' => 'Outlet Merdeka',
            'si_company_site' => false,
            'si_company' => 'GJB',
            'si_active' => true,
            'created_by' => 'ADMIN',
            'updated_by' => 'ADMIN',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}

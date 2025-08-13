<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $last_dep = Carbon::now()->subMonth()->endOfMonth()->toDateString();
      // dd(Carbon::now()->subMonth()->endOfMonth()->toDateString());
        Company::create([
            'co_company' => 'GEJ 2',
            'co_name' => 'PT. GADAI ELEKTRONIK JAKARTA 2',
            'co_active' => true,
            'last_dep' => $last_dep,
        ]);

        Company::create([
            'co_company' => 'AG2',
            'co_name' => 'PT. AMANAH TERIMA GADAI 2',
            'co_active' => true,
            'last_dep' => $last_dep,
        ]);

        Company::create([
            'co_company' => 'AG1',
            'co_name' => 'PT. AMANAH TERIMA GADAI',
            'co_active' => true,
            'last_dep' => $last_dep,
        ]);

        Company::create([
          'co_company' => 'IJG1',
          'co_name' => 'PT. INDAH JAYA GADAI',
          'co_active' => true,
          'last_dep' => $last_dep,
      ]);

        Company::create([
          'co_company' => 'KDG',
          'co_name' => 'PT. KUSUMA DWIPA GADAI',
          'co_active' => true,
          'last_dep' => $last_dep,
      ]);

      Company::create([
        'co_company' => 'GM1',
        'co_name' => 'PT. GADAI MENUJU SUKSES',
        'co_active' => true,
        'last_dep' => $last_dep,
      ]);

      Company::create([
        'co_company' => 'SAG',
        'co_name' => 'PT. SEMERU AGUNG GADAI',
        'co_active' => true,
        'last_dep' => $last_dep,
      ]);

      Company::create([
        'co_company' => 'GEJ',
        'co_name' => 'PT. GADAI ELEKTRONIK JAKARTA',
        'co_active' => true,
        'last_dep' => $last_dep,
      ]);

      Company::create([
        'co_company' => 'SIG',
        'co_name' => 'PT. SETIA INDAH GADAI',
        'co_active' => true,
        'last_dep' => $last_dep,
      ]);

      Company::create([
        'co_company' => 'LWG',
        'co_name' => 'PT. LAUTAN WIRANI GADAI',
        'co_active' => true,
        'last_dep' => $last_dep,
      ]);

      Company::create([
          'co_company' => 'GJB',
          'co_name' => 'PT. GADAI JADI BERKAH',
          'co_active' => true,
          'last_dep' => $last_dep,
      ]);
    }
}

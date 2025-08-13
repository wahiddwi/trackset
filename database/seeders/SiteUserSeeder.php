<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteUser;

class SiteUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SiteUser::create([
            'su_user' => 1,
            'su_site' => 'H01',
            'su_default' => true,
        ]);
    }
}

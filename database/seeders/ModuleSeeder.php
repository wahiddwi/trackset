<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Header Setting
        Module::create([
            'mod_code' => 'HDR_001',
            'mod_name' => 'Setting',
            'mod_path' => '#',
            'mod_desc' => 'Master Setting',
            'mod_icon' => 'fa fa-cogs',
            'mod_parent' => null,
            'mod_active' => true,
            'mod_order' => 0,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Menu Module
        Module::create([
            'mod_code' => 'MNU_001',
            'mod_name' => 'Module',
            'mod_path' => 'modules',
            'mod_desc' => 'Module tab',
            'mod_icon' => 'fas fa-desktop',
            'mod_parent' => '1',
            'mod_active' => true,
            'mod_order' => 0,
            'mod_superuser' => true,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Header Master Data
        Module::create([
            'mod_code' => 'HDR_002',
            'mod_name' => 'Master Data',
            'mod_path' => '#',
            'mod_desc' => 'Master Data',
            'mod_icon' => 'fa-solid fa-box-archive',
            'mod_parent' => null,
            'mod_active' => true,
            'mod_order' => 1,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Sub Header Employee Settings
        Module::create([
            'mod_code' => 'SUB_HDR_001',
            'mod_name' => 'Employee Settings',
            'mod_path' => '#',
            'mod_desc' => 'Header Master Data Employee',
            'mod_icon' => 'fa fa-users-cog',
            'mod_parent' => '3',
            'mod_active' => true,
            'mod_order' => 0,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Menu Roles
        Module::create([
            'mod_code' => 'MNU_002',
            'mod_name' => 'Roles',
            'mod_path' => 'roles',
            'mod_desc' => 'Master Roles',
            'mod_icon' => 'fa fa-briefcase',
            'mod_parent' => '4',
            'mod_active' => true,
            'mod_order' => 0,
            'mod_superuser' => true,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Menu Users
        Module::create([
            'mod_code' => 'MNU_003',
            'mod_name' => 'Users',
            'mod_path' => 'users',
            'mod_desc' => 'Master Users',
            'mod_icon' => 'fa fa-users',
            'mod_parent' => '4',
            'mod_active' => true,
            'mod_order' => 1,
            'mod_superuser' => true,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        // end sub header Employee Settings

        // Sub Header Site Settings
        Module::create([
            'mod_code' => 'SUB_HDR_002',
            'mod_name' => 'Site Settings',
            'mod_path' => '#',
            'mod_desc' => 'Header Master Data Site',
            'mod_icon' => 'fa fa-users-cog',
            'mod_parent' => '3',
            'mod_active' => true,
            'mod_order' => 1,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Menu Company
        Module::create([
            'mod_code' => 'MNU_004',
            'mod_name' => 'Company',
            'mod_path' => 'companies',
            'mod_desc' => 'Master Company',
            'mod_icon' => 'fa fa-university',
            'mod_parent' => '7',
            'mod_active' => true,
            'mod_order' => 0,
            'mod_superuser' => true,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Menu Site
        Module::create([
            'mod_code' => 'MNU_005',
            'mod_name' => 'Site',
            'mod_path' => 'sites',
            'mod_desc' => 'Master Site',
            'mod_icon' => 'fa fa-store',
            'mod_parent' => '7',
            'mod_active' => true,
            'mod_order' => 1,
            'mod_superuser' => true,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Menu Locations
        Module::create([
            'mod_code' => 'MNU_006',
            'mod_name' => 'Locations',
            'mod_path' => 'location',
            'mod_desc' => 'Master Location',
            'mod_icon' => 'fas fa-warehouse',
            'mod_parent' => '7',
            'mod_active' => true,
            'mod_order' => 2,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        // end sub header Site Settings

        // Menu Account
        Module::create([
            'mod_code' => 'MNU_007',
            'mod_name' => 'Account',
            'mod_path' => 'account',
            'mod_desc' => 'Master Account',
            'mod_icon' => 'fa-solid fa-bookmark',
            'mod_parent' => '3',
            'mod_active' => true,
            'mod_order' => 2,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Menu Depreciation
        Module::create([
            'mod_code' => 'MNU_008',
            'mod_name' => 'Depreciations',
            'mod_path' => 'cat-depreciations',
            'mod_desc' => 'Master Penyusutan',
            'mod_icon' => 'fa fa-chart-column',
            'mod_parent' => '3',
            'mod_active' => true,
            'mod_order' => 2,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Menu Category
        Module::create([
            'mod_code' => 'MNU_009',
            'mod_name' => 'Category',
            'mod_path' => 'categories',
            'mod_desc' => 'Master Category',
            'mod_icon' => 'fa-solid fa-list',
            'mod_parent' => '3',
            'mod_active' => true,
            'mod_order' => 3,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Menu Asset
        Module::create([
            'mod_code' => 'MNU_010',
            'mod_name' => 'Assets',
            'mod_path' => 'asset',
            'mod_desc' => 'Master Asset',
            'mod_icon' => 'fa fa-hand-holding-dollar',
            'mod_parent' => '3',
            'mod_active' => true,
            'mod_order' => 4,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Menu Vehicle
        Module::create([
            'mod_code' => 'MNU_011',
            'mod_name' => 'Vehicle',
            'mod_path' => 'vehicle',
            'mod_desc' => 'Master Vehicle',
            'mod_icon' => 'fa-solid fa-car-rear',
            'mod_parent' => '3',
            'mod_active' => true,
            'mod_order' => 5,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Header Transaction
        Module::create([
            'mod_code' => 'HDR_003',
            'mod_name' => 'Transactions',
            'mod_path' => '#',
            'mod_desc' => 'Modul Transaksi',
            'mod_icon' => 'fa fa-receipt',
            'mod_parent' => null,
            'mod_active' => true,
            'mod_order' => 2,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Menu Transfer
        Module::create([
            'mod_code' => 'MNU_012',
            'mod_name' => 'Transfer',
            'mod_path' => 'transfer',
            'mod_desc' => 'Modul Transfer',
            'mod_icon' => 'fa-solid fa-paper-plane',
            'mod_parent' => '16',
            'mod_active' => true,
            'mod_order' => 0,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Menu Receive
        Module::create([
            'mod_code' => 'MNU_013',
            'mod_name' => 'Receive',
            'mod_path' => 'receive',
            'mod_desc' => 'Modul Receive',
            'mod_icon' => 'fa-solid fa-envelope-circle-check',
            'mod_parent' => '16',
            'mod_active' => true,
            'mod_order' => 1,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Header Report
        Module::create([
            'mod_code' => 'HDR_004',
            'mod_name' => 'Report',
            'mod_path' => '#',
            'mod_desc' => 'Header Report',
            'mod_icon' => 'fa-regular fa-folder-open',
            'mod_parent' => null,
            'mod_active' => true,
            'mod_order' => 3,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Menu Inventory
        Module::create([
            'mod_code' => 'MNU_014',
            'mod_name' => 'Inventory',
            'mod_path' => 'inventory',
            'mod_desc' => 'Modul Inventory',
            'mod_icon' => 'fa fa-warehouse',
            'mod_parent' => '19',
            'mod_active' => true,
            'mod_order' => 0,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Header Journal
        Module::create([
            'mod_code' => 'HDR_005',
            'mod_name' => 'Journal',
            'mod_path' => '#',
            'mod_desc' => 'Header Penjualan',
            'mod_icon' => 'fa-solid fa-square-rss',
            'mod_parent' => null,
            'mod_active' => true,
            'mod_order' => 4,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Menu Periode
        Module::create([
            'mod_code' => 'MNU_015',
            'mod_name' => 'Periode',
            'mod_path' => 'periode',
            'mod_desc' => 'Modul Master Periode',
            'mod_icon' => 'fa-solid fa-calendar-days',
            'mod_parent' => '21',
            'mod_active' => true,
            'mod_order' => 0,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Menu JOurnal Asset
        Module::create([
            'mod_code' => 'MNU_016',
            'mod_name' => 'Journal Asset',
            'mod_path' => 'journal-asset',
            'mod_desc' => 'Modul Journal Asset',
            'mod_icon' => 'fa-solid fa-percent',
            'mod_parent' => '21',
            'mod_active' => true,
            'mod_order' => 1,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Sub Menu Disposal
        Module::create([
            'mod_code' => 'SUB_HDR_003',
            'mod_name' => 'Journal Disposal',
            'mod_path' => 'disposal',
            'mod_desc' => 'Modul Journal Disposal',
            'mod_icon' => 'fa-solid fa-dollar-sign',
            'mod_parent' => '21',
            'mod_active' => true,
            'mod_order' => 2,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Menu Journal Disposal
        Module::create([
            'mod_code' => 'MNU_017',
            'mod_name' => 'Journal Disposal',
            'mod_path' => 'disposal',
            'mod_desc' => 'Modul Journal Disposal',
            'mod_icon' => 'fa-solid fa-magnifying-glass-dollar',
            'mod_parent' => '24',
            'mod_active' => true,
            'mod_order' => 0,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Menu Journal Selling
        Module::create([
            'mod_code' => 'MNU_018',
            'mod_name' => 'Journal Selling',
            'mod_path' => 'selling',
            'mod_desc' => 'Modul Journal Selling',
            'mod_icon' => 'fa-solid fa-rupiah-sign',
            'mod_parent' => '24',
            'mod_active' => true,
            'mod_order' => 1,
            'mod_superuser' => false,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // // Menu Journal Logs
        // Module::create([
        //     'mod_code' => 'MNU_018',
        //     'mod_name' => 'Logs',
        //     'mod_path' => 'logs',
        //     'mod_desc' => 'Logs Journal',
        //     'mod_icon' => 'fa-solid fa-road-barrier',
        //     'mod_parent' => '21',
        //     'mod_active' => true,
        //     'mod_order' => 3,
        //     'mod_superuser' => false,
        //     'created_by' => '2403035',
        //     'updated_by' => '2403035',
        //     'created_at' => Carbon::now(),
        //     'updated_at' => Carbon::now(),
        // ]);

    }
}

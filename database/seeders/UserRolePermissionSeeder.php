<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permission
        Permission::firstOrCreate(['name' => 'HDR_001_view']); // Settings
        Permission::firstOrCreate(['name' => 'MNU_001_view']); // Menu Module
        Permission::firstOrCreate(['name' => 'MNU_001_create']);
        Permission::firstOrCreate(['name' => 'MNU_001_update']);
        Permission::firstOrCreate(['name' => 'MNU_001_print']);
        Permission::firstOrCreate(['name' => 'MNU_001_post']);
        Permission::firstOrCreate(['name' => 'MNU_001_delete']);
        Permission::firstOrCreate(['name' => 'HDR_002_view']); // Master Data
        Permission::firstOrCreate(['name' => 'SUB_HDR_001_view']); // Employee Settings
        Permission::firstOrCreate(['name' => 'MNU_002_view']); // Role
        Permission::firstOrCreate(['name' => 'MNU_002_create']);
        Permission::firstOrCreate(['name' => 'MNU_002_update']);
        Permission::firstOrCreate(['name' => 'MNU_002_print']);
        Permission::firstOrCreate(['name' => 'MNU_002_post']);
        Permission::firstOrCreate(['name' => 'MNU_002_delete']);
        Permission::firstOrCreate(['name' => 'MNU_003_view']); // User
        Permission::firstOrCreate(['name' => 'MNU_003_create']);
        Permission::firstOrCreate(['name' => 'MNU_003_update']);
        Permission::firstOrCreate(['name' => 'MNU_003_print']);
        Permission::firstOrCreate(['name' => 'MNU_003_post']);
        Permission::firstOrCreate(['name' => 'MNU_003_delete']);
        Permission::firstOrCreate(['name' => 'SUB_HDR_002_view']); // Site Settings
        Permission::firstOrCreate(['name' => 'MNU_004_view']); // Company
        Permission::firstOrCreate(['name' => 'MNU_004_create']);
        Permission::firstOrCreate(['name' => 'MNU_004_update']);
        Permission::firstOrCreate(['name' => 'MNU_004_print']);
        Permission::firstOrCreate(['name' => 'MNU_004_post']);
        Permission::firstOrCreate(['name' => 'MNU_004_delete']);
        Permission::firstOrCreate(['name' => 'MNU_005_view']); // Sites
        Permission::firstOrCreate(['name' => 'MNU_005_create']);
        Permission::firstOrCreate(['name' => 'MNU_005_update']);
        Permission::firstOrCreate(['name' => 'MNU_005_print']);
        Permission::firstOrCreate(['name' => 'MNU_005_post']);
        Permission::firstOrCreate(['name' => 'MNU_005_delete']);
        Permission::firstOrCreate(['name' => 'MNU_006_view']); // Locations
        Permission::firstOrCreate(['name' => 'MNU_006_create']);
        Permission::firstOrCreate(['name' => 'MNU_006_update']);
        Permission::firstOrCreate(['name' => 'MNU_006_print']);
        Permission::firstOrCreate(['name' => 'MNU_006_post']);
        Permission::firstOrCreate(['name' => 'MNU_006_delete']);
        Permission::firstOrCreate(['name' => 'MNU_007_view']); // Account
        Permission::firstOrCreate(['name' => 'MNU_007_create']);
        Permission::firstOrCreate(['name' => 'MNU_007_update']);
        Permission::firstOrCreate(['name' => 'MNU_007_print']);
        Permission::firstOrCreate(['name' => 'MNU_007_post']);
        Permission::firstOrCreate(['name' => 'MNU_007_delete']);
        Permission::firstOrCreate(['name' => 'MNU_008_view']); // Depreciations
        Permission::firstOrCreate(['name' => 'MNU_008_create']);
        Permission::firstOrCreate(['name' => 'MNU_008_update']);
        Permission::firstOrCreate(['name' => 'MNU_008_print']);
        Permission::firstOrCreate(['name' => 'MNU_008_post']);
        Permission::firstOrCreate(['name' => 'MNU_008_delete']);
        Permission::firstOrCreate(['name' => 'MNU_009_view']); // Category
        Permission::firstOrCreate(['name' => 'MNU_009_create']);
        Permission::firstOrCreate(['name' => 'MNU_009_update']);
        Permission::firstOrCreate(['name' => 'MNU_009_print']);
        Permission::firstOrCreate(['name' => 'MNU_009_post']);
        Permission::firstOrCreate(['name' => 'MNU_009_delete']);
        Permission::firstOrCreate(['name' => 'MNU_010_view']); // Asset
        Permission::firstOrCreate(['name' => 'MNU_010_create']);
        Permission::firstOrCreate(['name' => 'MNU_010_update']);
        Permission::firstOrCreate(['name' => 'MNU_010_print']);
        Permission::firstOrCreate(['name' => 'MNU_010_post']);
        Permission::firstOrCreate(['name' => 'MNU_010_delete']);
        Permission::firstOrCreate(['name' => 'MNU_011_view']); // Vehicle
        Permission::firstOrCreate(['name' => 'MNU_011_create']);
        Permission::firstOrCreate(['name' => 'MNU_011_update']);
        Permission::firstOrCreate(['name' => 'MNU_011_print']);
        Permission::firstOrCreate(['name' => 'MNU_011_post']);
        Permission::firstOrCreate(['name' => 'MNU_011_delete']);
        Permission::firstOrCreate(['name' => 'HDR_003_view']); // Transactions
        Permission::firstOrCreate(['name' => 'MNU_012_view']); // Transfer
        Permission::firstOrCreate(['name' => 'MNU_012_create']);
        Permission::firstOrCreate(['name' => 'MNU_012_update']);
        Permission::firstOrCreate(['name' => 'MNU_012_print']);
        Permission::firstOrCreate(['name' => 'MNU_012_post']);
        Permission::firstOrCreate(['name' => 'MNU_012_delete']);
        Permission::firstOrCreate(['name' => 'MNU_013_view']); // Receive
        Permission::firstOrCreate(['name' => 'MNU_013_create']);
        Permission::firstOrCreate(['name' => 'MNU_013_update']);
        Permission::firstOrCreate(['name' => 'MNU_013_print']);
        Permission::firstOrCreate(['name' => 'MNU_013_post']);
        Permission::firstOrCreate(['name' => 'MNU_013_delete']);
        Permission::firstOrCreate(['name' => 'HDR_004_view']); // Report
        Permission::firstOrCreate(['name' => 'MNU_014_view']); // Inventory
        Permission::firstOrCreate(['name' => 'MNU_014_create']);
        Permission::firstOrCreate(['name' => 'MNU_014_update']);
        Permission::firstOrCreate(['name' => 'MNU_014_print']);
        Permission::firstOrCreate(['name' => 'MNU_014_post']);
        Permission::firstOrCreate(['name' => 'MNU_014_delete']);
        Permission::firstOrCreate(['name' => 'HDR_005_view']); // Journal
        Permission::firstOrCreate(['name' => 'MNU_015_view']); // Periode
        Permission::firstOrCreate(['name' => 'MNU_015_create']);
        Permission::firstOrCreate(['name' => 'MNU_015_update']);
        Permission::firstOrCreate(['name' => 'MNU_015_print']);
        Permission::firstOrCreate(['name' => 'MNU_015_post']);
        Permission::firstOrCreate(['name' => 'MNU_015_delete']);
        Permission::firstOrCreate(['name' => 'MNU_016_view']); // Journal Asset
        Permission::firstOrCreate(['name' => 'MNU_016_create']);
        Permission::firstOrCreate(['name' => 'MNU_016_update']);
        Permission::firstOrCreate(['name' => 'MNU_016_print']);
        Permission::firstOrCreate(['name' => 'MNU_016_post']);
        Permission::firstOrCreate(['name' => 'MNU_016_delete']);
        Permission::firstOrCreate(['name' => 'SUB_HDR_003_view']); // Disposal
        Permission::firstOrCreate(['name' => 'MNU_017_view']); // Journal Disposal
        Permission::firstOrCreate(['name' => 'MNU_017_create']);
        Permission::firstOrCreate(['name' => 'MNU_017_update']);
        Permission::firstOrCreate(['name' => 'MNU_017_print']);
        Permission::firstOrCreate(['name' => 'MNU_017_post']);
        Permission::firstOrCreate(['name' => 'MNU_017_delete']);
        Permission::firstOrCreate(['name' => 'MNU_018_view']); // Journal Selling
        Permission::firstOrCreate(['name' => 'MNU_018_create']);
        Permission::firstOrCreate(['name' => 'MNU_018_update']);
        Permission::firstOrCreate(['name' => 'MNU_018_print']);
        Permission::firstOrCreate(['name' => 'MNU_018_post']);
        Permission::firstOrCreate(['name' => 'MNU_018_delete']);
        Permission::firstOrCreate(['name' => 'MNU_019_view']); // Logsa
        Permission::firstOrCreate(['name' => 'MNU_019_create']);
        Permission::firstOrCreate(['name' => 'MNU_019_update']);
        Permission::firstOrCreate(['name' => 'MNU_019_print']);
        Permission::firstOrCreate(['name' => 'MNU_019_post']);
        Permission::firstOrCreate(['name' => 'MNU_019_delete']);

        // create roles and assign created permissions may be done by chaining
        $role = Role::create([
                'name' => 'SUPERADMIN',
                'role_active' => true,
                'role_name' => 'Super Admin',
                'created_by' => 'ADMIN',
                'updated_by' => 'ADMIN',
                ])->givePermissionTo([
                    'HDR_001_view', // Setting
                    'MNU_001_view', // Module
                    'MNU_001_create',
                    'MNU_001_update',
                    'MNU_001_print',
                    'MNU_001_post',
                    'HDR_002_view', // Master Data
                    'SUB_HDR_001_view', // Employee Settings
                    'MNU_002_view', // Role
                    'MNU_002_create',
                    'MNU_002_update',
                    'MNU_002_print',
                    'MNU_002_post',
                    'MNU_003_view', // User
                    'MNU_003_create',
                    'MNU_003_update',
                    'MNU_003_print',
                    'MNU_003_post',
                    'SUB_HDR_002_view', // Site Settings
                    'MNU_004_view', // Company
                    'MNU_004_create',
                    'MNU_004_update',
                    'MNU_004_print',
                    'MNU_004_post',
                    'MNU_005_view', // Site
                    'MNU_005_create',
                    'MNU_005_update',
                    'MNU_005_print',
                    'MNU_005_post',
                    'MNU_006_view', // Location
                    'MNU_006_create',
                    'MNU_006_update',
                    'MNU_006_print',
                    'MNU_006_post',
                    'MNU_007_view', // Account
                    'MNU_007_create',
                    'MNU_007_update',
                    'MNU_007_print',
                    'MNU_007_post',
                    'MNU_008_view', // Depreciation
                    'MNU_008_create',
                    'MNU_008_update',
                    'MNU_008_print',
                    'MNU_008_post',
                    'MNU_009_view', // Category
                    'MNU_009_create',
                    'MNU_009_update',
                    'MNU_009_print',
                    'MNU_009_post',
                    'MNU_010_view', // Asset
                    'MNU_010_create',
                    'MNU_010_update',
                    'MNU_010_print',
                    'MNU_010_post',
                    'MNU_011_view', // Vahicle
                    'MNU_011_create',
                    'MNU_011_update',
                    'MNU_011_print',
                    'MNU_011_post',
                    'HDR_003_view', // Transactions
                    'MNU_012_view', // Transfer
                    'MNU_012_create',
                    'MNU_012_update',
                    'MNU_012_print',
                    'MNU_012_post',
                    'MNU_013_view', // Receive
                    'MNU_013_create',
                    'MNU_013_update',
                    'MNU_013_print',
                    'MNU_013_post',
                    'HDR_004_view', // Report
                    'MNU_014_view', // Inventory
                    'MNU_014_create',
                    'MNU_014_update',
                    'MNU_014_print',
                    'HDR_005_view', // Journal
                    'MNU_015_view', // Periode
                    'MNU_015_create',
                    'MNU_015_update',
                    'MNU_015_print',
                    'MNU_015_post',
                    // 'MNU_015_delete',
                    'MNU_016_view', // Journal Asset
                    'MNU_016_create',
                    'MNU_016_update',
                    'MNU_016_print',
                    'MNU_016_post',
                    // 'MNU_016_delete',
                    'SUB_HDR_003_view', // Disposal
                    'MNU_017_view', // Journal Disposal
                    'MNU_017_create',
                    'MNU_017_update',
                    'MNU_017_print',
                    'MNU_017_post',
                    // 'MNU_017_delete',
                    'MNU_018_view', // Journal Selling
                    'MNU_018_create',
                    'MNU_018_update',
                    'MNU_018_print',
                    'MNU_018_post',
                    // 'MNU_018_delete',
                    'MNU_019_view', // Logs
                    'MNU_019_create',
                    'MNU_019_update',
                    'MNU_019_print',
                    'MNU_019_post',
                    // 'MNU_019_delete',
                ]);

        $user = User::create([
            'usr_nik' => '2403035',
            'usr_name' => 'Administrator',
            'password' => bcrypt('Asdqwe123'),
            'role_id' => 1,
            'usr_status' => true,
            'remember_token' => null,
            'created_by' => '2403035',
            'updated_by' => '2403035',
            'usr_email' => 'admin@example.com',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $user->assignRole($role->name);
    }
}

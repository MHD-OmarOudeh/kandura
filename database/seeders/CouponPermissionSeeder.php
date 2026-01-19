<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CouponPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'manage coupons',
            'view coupons',
            'create coupons',
            'update coupons',
            'delete coupons',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign to roles
        $admin = Role::where('name', 'admin')->first();
        $superAdmin = Role::where('name', 'super_admin')->first();

        if ($admin) {
            $admin->givePermissionTo([
                'manage coupons',
                'view coupons',
                'create coupons',
                'update coupons',
                'delete coupons',
            ]);
        }

        if ($superAdmin) {
            $superAdmin->givePermissionTo([
                'manage coupons',
                'view coupons',
                'create coupons',
                'update coupons',
                'delete coupons',
            ]);
        }

        $this->command->info('Coupon permissions created successfully!');
    }
}

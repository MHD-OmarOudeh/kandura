<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class OrderPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'manage orders',
            'view all orders',
            'update order status',
            'cancel any order',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $admin = Role::where('name', 'admin')->first();
        $superAdmin = Role::where('name', 'super_admin')->first();

        if ($admin) {
            $admin->givePermissionTo([
                'manage orders',
                'view all orders',
                'update order status',
            ]);
        }

        if ($superAdmin) {
            $superAdmin->givePermissionTo([
                'manage orders',
                'view all orders',
                'update order status',
                'cancel any order',
            ]);
        }

        $this->command->info('Order permissions created and assigned successfully!');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class WalletPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'manage wallet',
            'view wallet',
            'deposit to wallet',
            'withdraw from wallet',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign to roles
        $admin = Role::where('name', 'admin')->first();
        $superAdmin = Role::where('name', 'super_admin')->first();

        if ($admin) {
            $admin->givePermissionTo([
                'manage wallet',
                'view wallet',
                'deposit to wallet',
                'withdraw from wallet',
            ]);
        }

        if ($superAdmin) {
            $superAdmin->givePermissionTo([
                'manage wallet',
                'view wallet',
                'deposit to wallet',
                'withdraw from wallet',
            ]);
        }

        $this->command->info('Wallet permissions created successfully!');
    }
}

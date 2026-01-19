<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            CitiesSeeder::class,
            MeasurementSeeder::class,
            DesignOptionSeeder::class,


            // Permissions & Roles

            CouponPermissionSeeder::class,
            OrderPermissionSeeder::class,
            WalletPermissionSeeder::class,

            // أي Seeders إضافية عندك ضيفها هون
        ]);
    }
}

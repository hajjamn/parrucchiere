<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductionSimulationSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ClientSeeder::class,
            ServiceSeeder::class,
            ServiceLogSeeder::class,
        ]);
    }
}

/*
php artisan migrate:fresh
php artisan migrate:reset

php artisan migrate --path=database/migrations/2014_10_12_000000_create_users_table.php
php artisan migrate --path=database/migrations/2014_10_12_100000_create_password_reset_tokens_table.php
php artisan migrate --path=database/migrations/2019_08_19_000000_create_failed_jobs_table.php
php artisan migrate --path=database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php
php artisan migrate --path=database/migrations/2025_04_02_093021_create_clients_table.php
php artisan migrate --path=database/migrations/2025_04_02_093033_create_services_table.php
php artisan migrate --path=database/migrations/2025_04_22_070104_create_service_logs_table.php

php artisan db:seed --class=ProductionSimulationSeeder

php artisan migrate --path=database/migrations/2025_06_01_132921_add_commission_percentage_to_service_logs_table.php
php artisan migrate --path=database/migrations/2025_06_01_142915_add_uses_quantity_to_services_table.php

php artisan migrate --path=database/migrations/2025_06_02_120106_fix_existing_data.php



php artisan migrate --path=database/migrations/2025_06_21_153249_add_is_part_of_subscription_to_service_logs_table.php
php artisan migrate --path=database/migrations/2025_06_21_153555_add_custom_commission_to_service_logs.php
php artisan migrate --path=database/migrations/2025_06_21_154429_fix_existing_data_after_subscription_update.php
 */
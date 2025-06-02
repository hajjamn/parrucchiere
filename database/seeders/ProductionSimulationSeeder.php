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

/* php artisan migrate --path=database/migrations/2014_10_12_000000_create_users_table.php
php artisan migrate --path=database/migrations/2014_10_12_100000_create_password_reset_tokens_table.php
php artisan migrate --path=database/migrations/2019_08_19_000000_create_failed_jobs_table.php
php artisan migrate --path=database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php
php artisan migrate --path=database/migrations/2025_04_02_093021_create_clients_table.php
php artisan migrate --path=database/migrations/2025_04_02_093033_create_services_table.php
php artisan migrate --path=database/migrations/2025_04_22_070104_create_service_logs_table.php

php artisan db:seed --class=ProductionSimulationSeeder
 */
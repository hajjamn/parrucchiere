<?php

namespace Database\Seeders;

use App\Models\ServiceLog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ServiceLog::factory()->count(500)->create();
    }
}

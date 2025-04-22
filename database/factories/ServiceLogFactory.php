<?php

namespace Database\Factories;

use App\Models\ServiceLog;
use App\Models\User;
use App\Models\Client;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceLog>
 */
class ServiceLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = ServiceLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'client_id' => Client::inRandomOrder()->value('id'),
            'service_id' => Service::inRandomOrder()->value('id'),
            'performed_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\ServiceLog;
use App\Models\User;
use App\Models\Client;
use App\Models\Service;
use Illuminate\Support\Carbon;
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
        $service = Service::inRandomOrder()->first();
        $customPrice = null;

        if ($service->is_variable_price) {
            if ($service->price) {
                $quantity = fake()->numberBetween(1, 10);
                $customPrice = $quantity * $service->price;
            } else {
                $customPrice = fake()->randomFloat(2, 5, 100);
            }
        }

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'client_id' => Client::inRandomOrder()->first()->id,
            'service_id' => $service->id,
            'performed_at' => Carbon::now()->subDays(fake()->numberBetween(0, 30)),
            'custom_price' => $customPrice,
        ];
    }
}
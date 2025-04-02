<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->numberBetween('2', '6'), /* We need to pick a random user with ID between 2 and 6 */
            'client_id' => $this->faker->numberBetween('1', '50'), /* We need to pick a random client with ID between 1 and 50 */
            'service_time' => $this->faker->dateTimeBetween('-3 years', 'today'),

        ];
    }
}

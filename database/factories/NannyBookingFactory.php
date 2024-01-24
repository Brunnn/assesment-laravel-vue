<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NannyBooking>
 */
class NannyBookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startAt = $this->faker->dateTimeBetween('-1 year', '+1 year');
        $endAt = $this->faker->dateTimeBetween($startAt, '+1 year');
        return [
            'title' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 0, 1000),
            'start_at' => $startAt,
            'end_at' => $endAt,
        ];
    }
}

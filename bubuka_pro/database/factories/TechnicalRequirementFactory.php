<?php

namespace Database\Factories;

use App\Models\ChangeLog;
use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends Factory<ChangeLog>
 */
class TechnicalRequirementFactory extends Factory
{


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'os_type' => fake()->word(),
            'specifications' => fake()->sentence
        ];
    }
}

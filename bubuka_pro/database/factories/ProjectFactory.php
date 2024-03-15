<?php

namespace Database\Factories;

use App\Models\ChangeLog;
use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends Factory<ChangeLog>
 */
class ProjectFactory extends Factory
{


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'description' => fake()->sentence,
            'admin_id' => fake()->numerify
        ];
    }
}

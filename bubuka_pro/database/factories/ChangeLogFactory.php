<?php

namespace Database\Factories;

use App\Models\ChangeLog;
use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends Factory<ChangeLog>
 */
class ChangeLogFactory extends Factory
{


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'changes' => fake()->sentence,
            'news' => fake()->sentence
        ];
    }
}

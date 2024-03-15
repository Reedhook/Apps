<?php

namespace Database\Factories;

use App\Models\Release;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReleaseDownloadFactory extends Factory
{


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $release = Release::factory()->create(); // Создание тестовой записи релиза
        // Подготовка данных для создания новой записи
        $utm = [
            'utm_source' => 'example_source',
            'utm_medium' => 'example_medium',
            'utm_campaign' => 'example_campaign',
        ];
        $utm = json_encode($utm);
        return [
            'ip' => fake()->ipv4, // Генерация фейкового IPv4-адреса
            'user_agent' => fake()->userAgent, // Генерация фейкового User-Agent
            'utm' => $utm,
            'release_id' => $release['id'], // Идентификатор созданной записи релиза
        ];
    }
}

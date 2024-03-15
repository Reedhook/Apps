<?php

/** Тестирования метода create, контроллера CreateController of ReleaseDownload */

namespace Tests\Feature\ReleaseDownload;

use App\Models\Release;
use App\Http\Controllers\ReleaseDownload\CreateController as ReleaseDownloadController;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;
    protected ReleaseDownloadController $rd;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rd = new ReleaseDownloadController();
    }

    /**
     * Тестирование метода CreateController@create()
     * @test
     * @throws Exception
     */
    public function created_new_records()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $release = Release::factory()->create(); // Создание тестовой записи релиза
        // Подготовка данных для создания новой записи
        $data = [
            'ip' => fake()->ipv4, // Генерация фейкового IPv4-адреса
            'user_agent' => fake()->userAgent, // Генерация фейкового User-Agent
            'utm' => [
                'utm_source' => 'example_source',
                'utm_medium' => 'example_medium',
                'utm_campaign' => 'example_campaign',
            ],
            'release_id' => $release['id'], // Идентификатор созданной записи релиза
        ];


        $this->rd->store($data['ip'], $data['user_agent'], $data['utm'], $data['release_id']); // Вызов метода store для создания новой записи
        $this->assertDatabaseCount('releases_downloads', 1); // Проверка количества записей в таблице releases_downloads
    }

    /**
     * Тестирование исключения NotFound
     * @test
     */
    public function test_ModelNotFoundException()
    {
        $this->expectException(ModelNotFoundException::class);
        // Подготовка данных для создания новой записи
        $data = [
            'ip' => fake()->ipv4, // Генерация фейкового IPv4-адреса
            'user_agent' => fake()->userAgent, // Генерация фейкового User-Agent
            'utm' => [
                'utm_source' => 'example_source',
                'utm_medium' => 'example_medium',
                'utm_campaign' => 'example_campaign',
            ],
            'release_id' => 2, // Идентификатор созданной записи релиза
        ];
        $this->rd->store($data['ip'], $data['user_agent'], $data['utm'], $data['release_id']); // Вызов метода store для создания новой записи
    }
}

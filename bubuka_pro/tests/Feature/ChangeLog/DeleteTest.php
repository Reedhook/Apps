<?php

/** Тестирования метода delete, контроллера DeleteController of ChangeLog */

namespace Tests\Feature\ChangeLog;

use App\Models\ChangeLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    protected $userToken;
    protected ChangeLog|Collection|Model $changelog;

    protected function setUp(): void
    {
        parent::setUp();

        // При запуске тестов из данного файла создается Пользователь с JWT токеном
        $user = User::factory()->create();
        $this->userToken = JWTAuth::fromUser($user);

        // Создание фальшивых данных
        $this->changelog = ChangeLog::factory()->create();
    }

    /**
     * Тестирование метода  DeleteController@delete()
     * @test
     */
    public function deleted_records()
    {
        // Отключаем обработку исключении
        $this->withoutExceptionHandling();

        // Запрос на endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->delete('api/changes/'.$this->changelog['id']);

        // Проверка статуса ответа
        $response->assertStatus(200);

        // Поиск мягко удаленных данных
        $trash = ChangeLog::withTrashed()->find($this->changelog['id']);

        // Сравнение созданных и удаленных записей
        $this->assertEquals($this->changelog['changes'], $trash->changes);
    }

    /**
     * Тестирование исключения NotFound
     * @test
     */
    public function test_ModelNotFoundException()
    {
        // Запрос на endpoint. Так как мы создали только 1 запись в фальшивом бд, несмотря на существование данных в реальном бд, id будет начинаться с 1. Поэтому для проверки достаточно искать записи с id=2
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->delete('api/changes/2');

        // Проверка статуса
        $response->assertStatus(404);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_is_user()
    {
        // Запрос на endpoint без JWT токена
        $response = $this->delete('api/changes/'.$this->changelog['id']);

        // Проверка статуса ответа
        $response->assertStatus(401);
    }
}

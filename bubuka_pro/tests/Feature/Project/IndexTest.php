<?php

/** Тестирования метода index, контроллера IndexController of Project */

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    // Использование RefreshDatabase для очистки базы данных после каждого теста

    protected int $max; // Максимальное количество проектов
    protected $userToken; // Токен пользователя

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create(); // Создание пользователя
        $this->userToken = JWTAuth::fromUser($user); // Генерация токена для пользователя
        $this->max = 10; // Установка максимального количества проектов
        Project::factory($this->max)->create(); // Создание указанного количества проектов
    }

    /**
     * Тестирование метода  IndexController@index()
     * @test
     */
    public function response_for_route_projects_index()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->get('api/projects'); // Отправка GET-запроса для получения списка проектов

        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)

        $response = $response->json();

        $this->assertCount($this->max, $response['body']['projects']); // Проверка, что количество записей в ответе соответствует максимальному количеству
    }

    /**
     * Тестирование метода с limit IndexController@index()
     * @test
     */
    public function response_for_route_projects_index_with_limit()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $limit = 5; // Установка значения лимита
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->get("api/projects?limit={$limit}"); // Отправка GET-запроса для получения списка проектов с указанным лимитом
        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)

        $response = $response->json();

        $this->assertCount($limit, $response['body']['projects']); // Проверка, что количество записей в ответе соответствует установленному лимиту
    }

    /**
     * Тестирование метода с limit и offset IndexController@index()
     * @test
     */
    public function response_for_route_projects_index_with_limit_and_offset()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $limit = $this->max; // Установка значения лимита
        $offset = 2; // Установка значения смещения
        $difference = $limit - $offset; // Вычисление разницы между лимитом и смещением
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->get("api/projects?limit={$limit}&offset={$offset}"); // Отправка GET-запроса для получения списка проектов с указанным лимитом и смещением

        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)
        $response = $response->json();
        $this->assertCount($difference, $response['body']['projects']); // Проверка, что количество записей в ответе соответствует установленной разнице
    }

    /**
     * Тестирование метода с limit и offset IndexController@index()
     * @test
     */
    public function response_for_route_projects_index_with_offset_and_withOut_limit()
    {
        $offset = 2; // Установка значения смещения
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->get("api/projects?offset={$offset}"); // Отправка GET-запроса для получения списка проектов с указанным смещением без установленного лимита

        $response->assertStatus(422)->assertJson([ // Проверка, что запрос вернул статус 422 (Непрошедшая валидацию) и содержит указанную ошибку
            'errors' => [
                'limit' => ['The limit field is required when offset are present.'],
                // Проверка, что получен ответ с указанной ошибкой валидации
            ],
        ]);
    }

    /**
     * Тестирование валидации поля limit IndexRequest: на тип данных
     * @test
     */
    public function validation_limit_to_integer()
    {
        $limit = 'is_not_integer'; // Установка некорректного значения лимита
        $offset = 'is_not_integer'; // Установка некорректного значения смещения
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->get("api/projects?limit={$limit}&offset=$offset"); // Отправка GET-запроса для получения списка проектов с некорректным значением лимита

        $response->assertStatus(422)->assertJson([ // Проверка, что запрос вернул статус 422 (Непрошедшая валидацию) и содержит указанную ошибку
            'errors' => [
                'limit' => ['The limit field must be an integer.'],
                'offset' => ['The offset field must be an integer.']
                // Проверка, что получен ответ с указанной ошибкой валидации
            ],
        ]);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function user_is_user()
    {
        $response = $this->get('api/projects'); // Отправка GET-запроса для получения списка проектов без JWT токена

        $response->assertStatus(401); // Проверка, что запрос вернул статус 401 (Неавторизованный доступ)
    }
}

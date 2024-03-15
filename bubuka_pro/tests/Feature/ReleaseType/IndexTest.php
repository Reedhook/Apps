<?php

/** Тестирования метода index, контроллера IndexController of ReleaseType */

namespace Tests\Feature\ReleaseType;

use App\Models\ReleaseType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class IndexTest extends TestCase
{
    use RefreshDatabase; // Использование RefreshDatabase для очистки базы данных после каждого теста

    protected int $max; // Максимальное количество записей
    protected $userToken; // Токен пользователя

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create(); // Создание пользователя
        $this->userToken = JWTAuth::fromUser($user); // Генерация токена для пользователя
        $this->max = 10; // Установка максимального количества записей
        ReleaseType::factory($this->max)->create(); // Создание указанного количества записей типов релизов
    }

    /**
     * Тестирование метода IndexController@index()
     * @test
     */
    public function response_for_route_releases_types_index()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->get('api/releases_types'); // Отправка GET-запроса для получения всех типов релизов

        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)
        $response = $response->json(); // Преобразование ответа в JSON формат
        $this->assertCount($this->max,  $response['body']['releases_types']); // Проверка, что количество записей соответствует максимальному количеству
    }

    /**
     * Тестирование метода с limit IndexController@index()
     * @test
     */
    public function response_for_route_releases_types_index_with_limit()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $limit = 5;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->get("api/releases_types?limit={$limit}"); // Отправка GET-запроса для получения указанного количества записей типов релизов

        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)
        $response = $response->json(); // Преобразование ответа в JSON формат
        $this->assertCount($limit,  $response['body']['releases_types']); // Проверка, что количество записей соответствует указанному лимиту
    }

    /**
     * Тестирование метода с limit и offset IndexController@index()
     * @test
     */
    public function response_for_route_releases_types_index_with_limit_and_offset()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $limit = $this->max;
        $offset = 2;
        $difference = $limit - $offset;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->get("api/releases_types?limit={$limit}&offset={$offset}"); // Отправка GET-запроса для получения записей с указанным лимитом и оффсетом

        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)
        $response = $response->json(); // Преобразование ответа в JSON формат
        $this->assertCount($difference, $response['body']['releases_types']); // Проверка, что количество записей соответствует разнице между лимитом и оффсетом
    }

    /**
     * Тестирование метода с offset, но без limit IndexController@index()
     * @test
     */
    public function response_for_route_releases_types_index_with_offset_and_withOut_limit()
    {
        $offset = 2;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->get("api/releases_types?offset={$offset}"); // Отправка GET-запроса для получения записей с указанным оффсетом без лимита

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'limit' => ['The limit field is required when offset are present.'], // Проверка, что поле 'limit' обязательно при наличии поля 'offset'
            ],
        ]);
    }

    /**
     * Тестирование валидации поля limit IndexRequest: на тип данных
     * @test
     */
    public function validation_limit_to_integer()
    {
        $limit = 'is_not_integer';
        $offset = 'is_not_integer';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->get("api/releases_types?limit=$limit&offset=$offset"); // Отправка GET-запроса с некорректным значением поля 'limit'

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'limit' => ['The limit field must be an integer.'], // Проверка, что поле 'limit' должно быть целым числом
                'offset' => ['The offset field must be an integer.'], // Проверка, что поле 'offset' должно быть целым числом
            ],
        ]);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function user_is_user()
    {
        $response = $this->get('api/releases_types'); // Отправка GET-запроса для получения всех типов релизов без JWT токена

        $response->assertStatus(401); // Проверка, что запрос вернул статус 401 (Неавторизованный доступ)
    }
}

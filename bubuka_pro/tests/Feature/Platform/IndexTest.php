<?php

/** Тестирования метода index, контроллера IndexController of Platform */

namespace Tests\Feature\Platform;

use App\Models\Platform;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    protected int $max;
    protected $userToken;

    protected function setUp(): void
    {
        parent::setUp();

        // При запуске тестов из данного файла создается Пользователь с JWT токеном
        $user = User::factory()->create();
        $this->userToken = JWTAuth::fromUser($user);

        // Задаем максимальное количество записей для создания
        $this->max = 10;

        // Создаем записи в бд с factory
        Platform::factory($this->max)->create();
    }

    /**
     * Тестирование метода  IndexController@index()
     * @test
     */
    public function response_for_route_platforms_index()
    {
        // Отключаем обработку исключении
        $this->withoutExceptionHandling();

        //Запрос на endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->get('api/platforms');

        // Проверка статуса ответа
        $response->assertStatus(200);

        $response = $response->json();

        // Сравнение количества созданных и вернувшихся записей
        $this->assertCount($this->max, $response['body']['platforms']);
    }

    /**
     * Тестирование метода с limit IndexController@index()
     * @test
     */
    public function response_for_route_platforms_index_with_limit()
    {
        // Отключаем обработку исключении
        $this->withoutExceptionHandling();

        $limit = 5;

        // Отправка запроса на endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->get('api/platforms/?limit='.$limit);

        // Проверка статус ответа
        $response->assertStatus(200);

        $response = $response->json();

        // Сравнение limit c количеством вернувшихся записей
        $this->assertCount($limit, $response['body']['platforms']);
    }

    /**
     * Тестирование метода с limit и offset IndexController@index()
     * @test
     */
    public function response_for_route_platforms_index_with_limit_and_offset()
    {
        // Отключение обработки исключении
        $this->withoutExceptionHandling();

        // Задаем limit максимальное количество созданных записей
        $limit = $this->max;
        $offset = 2;
        $difference = $limit - $offset;

        // Отправка запроса на endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->get('api/platforms/?limit='.$limit.'&'.'offset='.$offset);

        // Проверка статус ответа
        $response->assertStatus(200);

        $response = $response->json();

        // Сравнение разницы между limit и offset с количеством вернувшихся записей
        $this->assertCount($difference, $response['body']['platforms']);
    }

    /**
     * Тестирование метода с offset, но без limit IndexController@index()
     * @test
     */
    public function response_for_route_platforms_index_with_offset_and_withOut_limit()
    {
        $offset = 2;

        // Отправка запроса на endpoint
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->get('api/platforms/?offset='.$offset);

        // Проверка статуса ответа и сообщения ответа
        $response->assertStatus(422)->assertJson([
            'errors' => [
                'limit' => ['The limit field is required when offset are present.'],
            ],
        ]);
    }

    /**
     * Тестирование валидации поля limit IndexRequest: на тип данных
     * @test
     */
    public function validation_limit_to_integer()
    {
        $limit = 'is_not_integer'; // Значение переменной $limit, которое не является целым числом
        $offset = 'is_not_integer'; // Значение переменной $offset, которое не является целым числом

        // Запрос на endpoint с передачей значения limit в качестве параметра
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->get("api/platforms/?limit=$limit&offset=$offset");

        // Проверка статуса ответа и содержимого JSON
        $response->assertStatus(422)->assertJson([
            'errors' => [
                'limit' => ['The limit field must be an integer.'], // Проверка наличия ошибки "limit должно быть целым числом"
                'offset' => ['The offset field must be an integer.'], // Проверка наличия ошибки "offset должно быть целым числом"
            ],
        ]);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function user_is_user()
    {
        // Запрос на endpoint без JWT токена
        $response = $this->get('api/platforms');

        // Проверка статуса ответа
        $response->assertStatus(401);
    }
}

<?php

/** Тестирования метода index, контроллера IndexController of TechnicalRequirement */

namespace Tests\Feature\TechnicalRequirement;

use App\Models\TechnicalRequirement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    // Использование RefreshDatabase для очистки базы данных после каждого теста

    protected int $max; // Максимальное количество записей
    protected $userToken; // Токен пользователя

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create(); // Создание тестового пользователя
        $this->userToken = JWTAuth::fromUser($user); // Генерация токена для пользователя
        $this->max = 10; // Установка максимального количества записей
        TechnicalRequirement::factory($this->max)->create(); // Создание указанного количества записей технических требований
    }

    /**
     * Тестирование метода IndexController@index()
     * @test
     */
    public function response_for_route_techs_reqs_index()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->get('api/techs_reqs'); // Отправка GET-запроса для получения всех технических требований

        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)

        $response = $response->json(); // Преобразование ответа в формат JSON
        $this->assertCount($this->max, $response['body']['technicals_requirements']); // Проверка количества полученных записей
    }

    /**
     * Тестирование метода с limit IndexController@index()
     * @test
     */
    public function response_for_route_techs_reqs_index_with_limit()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $limit = 5; // Установка значения параметра limit
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->get("api/techs_reqs?limit={$limit}"); // Отправка GET-запроса с параметром limit

        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)
        $response = $response->json();
        $this->assertCount($limit, $response['body']['technicals_requirements']); // Проверка количества полученных записей
    }

    /**
     * Тестирование метода с limit и offset IndexController@index()
     * @test
     */
    public function response_for_route_techs_reqs_index_with_limit_and_offset()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений

        $limit = $this->max; // Установка значения параметра limit
        $offset = 2; // Установка значения параметра offset
        $difference = $limit - $offset; // Вычисление разницы между limit и offset

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->get("api/techs_reqs?limit={$limit}&offset={$offset}"); // Отправка GET-запроса с параметрами limit и offset

        $response->assertStatus(200); // Проверка, что запрос вернул статус 200 (Успешное выполнение)

        $response = $response->json(); // Преобразование ответа в формат JSON
        $this->assertCount($difference, $response['body']['technicals_requirements']); // Проверка количества полученных записей
    }

    /**
     * Тестирование метода с limit и offset IndexController@index()
     * @test
     */
    public function response_for_route_techs_reqs_index_with_offset_and_withOut_limit()
    {
        $offset = 2; // Установка значения параметра offset
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->get("api/techs_reqs?offset={$offset}"); // Отправка GET-запроса с параметром offset

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'limit' => ['The limit field is required when offset are present.'],
                // Проверка сообщения об ошибке валидации
            ],
        ]);
    }

    /**
     * Тестирование валидации поля limit IndexRequest: на тип данных
     * @test
     */
    public function validation_limit_to_integer()
    {
        $limit = 'is_not_integer'; // Установка некорректного значения параметра limit
        $offset = 'is_not_integer'; // Установка некорректного значения параметра offset

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача токена в заголовке запроса
        ])->get("api/techs_reqs?limit=$limit&offset=$offset"); // Отправка GET-запроса с некорректным значением параметра limit

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'limit' => ['The limit field must be an integer.'], // Проверка сообщения об ошибке валидации
                'offset' => ['The offset field must be an integer.'], // Проверка сообщения об ошибке валидации
            ],
        ]);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function user_is_user()
    {
        $response = $this->get('api/techs_reqs'); // Отправка GET-запроса без JWT токена

        $response->assertStatus(401); // Проверка, что запрос вернул статус 401 (Неавторизованный доступ)
    }
}

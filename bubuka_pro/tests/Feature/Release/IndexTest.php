<?php

/** Тестирования метода index, контроллера IndexController of Release */

namespace Tests\Feature\Release;

use App\Models\Release;
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
        $user = User::factory()->create();
        $this->userToken = JWTAuth::fromUser($user);
        $this->max = 10;
        Release::factory($this->max)->create();
    }

    /**
     * Тестирование метода  IndexController@index()
     * @test
     */
    public function response_for_route_releases_index()
    {
        $this->withoutExceptionHandling();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->get('api/releases');

        $response->assertStatus(200);
        $response = $response->json();
        $this->assertCount($this->max, $response['body']['releases']);
    }

    /**
     * Тестирование метода с limit IndexController@index()
     * @test
     */
    public function response_for_route_releases_index_with_limit()
    {
        $this->withoutExceptionHandling();

        $limit = 5;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->get("api/releases?limit={$limit}");
        $response->assertStatus(200);
        $response = $response->json();
        $this->assertCount($limit, $response['body']['releases']);
    }

    /**
     * Тестирование метода с limit и offset IndexController@index()
     * @test
     */
    public function response_for_route_releases_index_with_limit_and_offset()
    {
        $this->withoutExceptionHandling();

        $limit = $this->max;
        $offset = 2;
        $difference = $limit - $offset;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->get("api/releases?limit={$limit}&offset={$offset}");

        $response->assertStatus(200);
        $response = $response->json();
        $this->assertCount($difference, $response['body']['releases']);
    }

    /**
     * Тестирование метода с limit и offset IndexController@index()
     * @test
     */
    public function response_for_route_releases_index_with_offset_and_withOut_limit()
    {
        $offset = 2;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->get("api/releases?offset={$offset}");
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
        $limit = 'is_not_integer';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->get("api/releases?limit={$limit}");

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'limit' => ['The limit field must be an integer.'],
            ],
        ]);
    }

    /**
     * Тестирование валидации поля offset IndexRequest: на тип данных
     * @test
     */
    public function validation_offset_to_integer()
    {
        $offset = 'is_not_integer';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->get("api/releases?limit={$this->max}&offset={$offset}");

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'offset' => ['The offset field must be an integer.'],
            ],
        ]);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function user_is_user()
    {
        $response = $this->get('api/releases');

        $response->assertStatus(401);
    }
}

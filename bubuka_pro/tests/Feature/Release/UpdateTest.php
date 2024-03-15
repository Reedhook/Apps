<?php

/** Тестирования метода update, контроллера UpdateController of Release */

namespace Tests\Feature\Release;

use App\Exceptions\SameDataException;
use App\Models\ChangeLog;
use App\Models\Platform;
use App\Models\Project;
use App\Models\Release;
use App\Models\ReleaseType;
use App\Models\TechnicalRequirement;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    protected Release|Collection|Model $release;
    protected User|Collection|Model $user;
    protected $userToken;
    protected File $file;
    protected ReleaseType|Collection|Model $rt;
    protected TechnicalRequirement|Collection|Model $tech;
    protected Platform|Collection|Model $platform;
    protected Project|Collection|Model $project;
    protected ChangeLog|Collection|Model $change;


    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->userToken = JWTAuth::fromUser($this->user);

        Storage::fake('local'); // Создание фейкового диска для хранения файлов
        $this->file = File::create(fake()->word()); // Создание фейкового файла

        $this->release = Release::factory()->create(); // Создание тестовой модели Release
        $this->project = Project::factory()->create(); // Создание тестовой модели Project
        $this->platform = Platform::factory()->create(); // Создание тестовой модели Platform
        $this->project->platforms()->attach($this->platform); // Привязка платформы к проекту
        $this->rt = ReleaseType::factory()->create(); // Создание тестовой модели ReleaseType
        $this->tech = TechnicalRequirement::factory()->create(); // Создание тестовой модели TechnicalRequirement
        $this->change = ChangeLog::factory()->create(); // Создание тестовой модели ChangeLog

    }

    /**
     * Тестирование метода  UpdateController@update()
     * @test
     */
    public function updated_record()
    {
        $this->withoutExceptionHandling(); // Отключение обработки исключений
        $project = Project::find($this->release->project_id);
        $project->platforms()->attach($this->platform); // Привязка платформы к проекту
        $data = [
            'file' => $this->file,
            'platform_id' => $this->platform->id,
            'change_id' => $this->change->id,
            'description' => fake()->sentence(),
            'release_type_id' => $this->rt->id,
            'is_ready' => false, // Готовность
            'technical_requirement_id' => $this->tech->id,
            'version' =>
                fake()->randomNumber(1).'.'.
                fake()->randomNumber(1).'.'.
                fake()->randomNumber(1)
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача JWT-токена в заголовке запроса
        ])->post("api/releases/{$this->release->id}", $data); // Отправка POST-запроса на обновление релиза
        $response->assertStatus(200); // Проверка успешного статуса ответа

        $this->assertDatabaseCount('releases', 1); // Проверка количества записей в таблице releases
    }

    /**
     * Тестирование валидации полей при создании релиза, StoreRequest: на тип данных
     * @test
     */
    public function validation_attributes_for_storing_releases_types()
    {
        $data = [
            'file' => 'is not file', // Некорректное значение для поля file
            'platform_id' => 'is not integer', // Некорректное значение для поля platform_id
            'change_id' => 'is not integer', // Некорректное значение для поля change_id
            'release_type_id' => 'is not integer', // Некорректное значение для поля release_type_id
            'technical_requirement_id' => 'is not integer', // Некорректное значение для поля technical_requirement_id
            'version' => 12345, // Некорректное значение для поля version
            'description' => 12345, // Некорректное значение для поля description
            'is_ready' => 'is not boolean', // Некорректное значение для поля is_ready
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача JWT-токена в заголовке запроса
        ])->post("api/releases/{$this->release->id}", $data); // Отправка POST-запроса на обновление релиза

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'file' => ['The file field must be a file.'], // Проверка ошибки валидации для поля file
                'platform_id' => ['The platform id field must be an integer.'],
                // Проверка ошибки валидации для поля platform_id
                'change_id' => ['The change id field must be an integer.'],
                // Проверка ошибки валидации для поля change_id
                'release_type_id' => ['The release type id field must be an integer.'],
                // Проверка ошибки валидации для поля release_type_id
                'technical_requirement_id' => ['The technical requirement id field must be an integer.'],
                // Проверка ошибки валидации для поля technical_requirement_id
                'version' => ['The version field must be a string.'], // Проверка ошибки валидации для поля version
                'description' => ['The description field must be a string.'],
                // Проверка ошибки валидации для поля description
                'is_ready' => ['The is ready field must be true or false.'],
                // Проверка ошибки валидации для поля is_ready
            ],
        ]);
    }

    /**
     * Тестирование валидации полей 'version' и 'description' при создании релиза, StoreRequest: на максимальную длину
     * @test
     */
    public function attributes_should_not_exceed_max_length()
    {
        $data = [
            'file' => $this->file, // Файл
            'project_id' => $this->release->project_id, // Id проекта
            'platform_id' => $this->release->platform_id, // Id платформы
            'change_id' => $this->release->change_id, // Id изменения
            'release_type_id' => $this->release->release_type_id, // Id типа релиза
            'is_ready' => false, // Готовность
            'technical_requirement_id' => $this->release->technical_requirement_id, // Id технического требования
            'description' => 'a'.str_repeat('b', 255), // Описание с максимальной длиной
            'version' => '1'.str_repeat('0', 10) // Версия с максимальной длиной
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken, // Передача JWT-токена в заголовке запроса
        ])->post("api/releases/{$this->release->id}", $data); // Отправка POST-запроса на обнов
        $response->assertStatus(422)->assertJson([
            'errors' => [
                'version' => ['The version field must not be greater than 10 characters.'],
                'description' => ['The description field must not be greater than 255 characters.'],
            ],
        ]);
    }

    /**
     * Тестирование исключения NotFound
     * @test
     */
    public function test_NotFound_exception()
    {
        $data = [
            'file' => $this->file,
            'project_id' => $this->release->project_id,
            'platform_id' => $this->release->platform_id,
            'change_id' => $this->release->change_id,
            'release_type_id' => $this->release->release_type_id,
            'is_ready' => false,
            'technical_requirement_id' => $this->release->technical_requirement_id,
            'description' => fake()->sentence(),
            'version' =>
                fake()->randomNumber(1).'.'.
                fake()->randomNumber(1).'.'.
                fake()->randomNumber(1),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post("api/releases/2", $data);
        $response->assertStatus(404);
    }

    /**
     * Тестирование исключения BadRequest
     * @test
     */
    public function test_same_data()
    {
        $this->expectException(SameDataException::class);
        $data = [
            'file' => $this->file,
            'project_id' => $this->project->id,
            'platform_id' => $this->platform->id,
            'change_id' => $this->change->id,
            'release_type_id' => $this->rt->id,
            'is_ready' => false,
            'technical_requirement_id' => $this->tech->id,
            'description' => 'NOT',
            'version' =>
                fake()->randomNumber(1).'.'.
                fake()->randomNumber(1).'.'.
                fake()->randomNumber(1),
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/releases', $data);

        $response = $response->json();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post("api/releases/".$response['body']['release']['id'], $data);
        $response->assertStatus(405)
            ->assertJson([
                'message' => "Данные идентичны, обновление не требуется"
            ]);
    }

    /**
     * Тестирование исключения Unauthorized
     * @test
     */
    public function test_user_is_user()
    {
        $data = [
            'file' => $this->file,
            'platform_id' => $this->release->platform_id,
            'change_id' => $this->release->change_id,
            'release_type_id' => $this->release->release_type_id,
            'is_ready' => $this->release->is_ready,
            'technical_requirement_id' => $this->release->technical_requirement_id,
            'description' => $this->release->description,
            'version' => $this->release->version,
        ];
        $response = $this->post("api/releases/{$this->release->id}", $data);

        $response->assertStatus(401);
    }
}

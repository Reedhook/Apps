<?php

/** Тестирования метода create, контроллера CreateController of Project */

namespace Tests\Feature\Release;

use App\Models\ChangeLog;
use App\Models\Platform;
use App\Models\Project;
use App\Models\ReleaseType;
use App\Models\TechnicalRequirement;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File as FT;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    protected $userToken;
    protected FT $file;
    protected Project|Collection|Model $project;
    protected Platform|Collection|Model $platform;
    protected ChangeLog|Collection|Model $change;
    protected ReleaseType|Collection|Model $rt;
    protected TechnicalRequirement|Collection|Model $tech;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create(['is_admin' => true]);
        $this->userToken = JWTAuth::fromUser($user);
        Storage::fake('local');

        $this->file = FT::create(fake()->name, fake()->randomNumber());
        $this->project = Project::factory()->create();
        $this->platform = Platform::factory()->create();
        $this->project->platforms()->attach($this->platform);
        $this->rt = ReleaseType::factory()->create();
        $this->tech = TechnicalRequirement::factory()->create();
        $this->change = ChangeLog::factory()->create();
    }

    /**
     * Тестирование метода  CreateController@create()
     * @test
     */
    public function created_new_records()
    {
        $this->withoutExceptionHandling();
        $data = [
            'file' => $this->file,
            'project_id' => $this->project->id,
            'platform_id' => $this->platform->id,
            'change_id' => $this->change->id,
            'description' => fake()->sentence(),
            'release_type_id' => $this->rt->id,
            'is_ready' => false,
            'technical_requirement_id' => $this->tech->id,
            'version' =>
                fake()->randomNumber(1).'.'.
                fake()->randomNumber(1).'.'.
                fake()->randomNumber(1),
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/releases', $data);

        $response->assertStatus(201);
        $this->assertDatabaseCount('releases', 1);

        $response = $response->json();
        $this->assertEquals($data['project_id'], $response['body']['release']['project_id']);
        $this->assertEquals($data['platform_id'], $response['body']['release']['platform_id']);
        $this->assertEquals($data['change_id'], $response['body']['release']['change_id']);
        $this->assertEquals($data['description'], $response['body']['release']['description']);
        $this->assertEquals($data['release_type_id'], $response['body']['release']['release_type_id']);
        $this->assertEquals($data['is_ready'], $response['body']['release']['is_ready']);
        $this->assertEquals($data['technical_requirement_id'], $response['body']['release']['technical_requirement_id']);
        $this->assertEquals($data['version'], $response['body']['release']['version']);
    }

    /**
     * Тестирование валидации полей 'file','project_id','platform_id', 'change_id', 'release_type_id', 'technical_requirement_id', 'version' при создании релиза, StoreRequest: на пустоту
     * @test
     */
    public function attribute_name_is_required_for_storing_release()
    {
        $data = [
            'file' => '',
            'project_id' => '',
            'platform_id' => '',
            'change_id' => '',
            'release_type_id' => '',
            'technical_requirement_id' => '',
            'version' => '',
        ];

        $response = $this
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->userToken,
            ])
            ->post('api/releases', $data);

        $response
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'file' => ['The file field is required.'],
                    'project_id' => ['The project id field is required.'],
                    'platform_id' => ['The platform id field is required.'],
                    'change_id' => ['The change id field is required.'],
                    'release_type_id' => ['The release type id field is required.'],
                    'technical_requirement_id' => ['The technical requirement id field is required.'],
                    'version' => ['The version field is required.'],
                ],
            ]);
    }

    /**
     * Тестирование валидации полей при создании релиза, StoreRequest: на тип данных
     * @test
     */
    public function validation_attributes_for_storing_releases_types()
    {
        $data = [
            'file' => 'is not file',
            'project_id' => 'is not integer',
            'platform_id' => 'is not integer',
            'change_id' => 'is not integer',
            'release_type_id' => 'is not integer',
            'technical_requirement_id' => 'is not integer',
            'version' => 12345,
            'description' => 12345,
            'is_ready' => 'is not boolean',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/releases', $data);

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'file' => ['The file field must be a file.'],
                'project_id' => ['The project id field must be an integer.'],
                'platform_id' => ['The platform id field must be an integer.'],
                'change_id' => ['The change id field must be an integer.'],
                'release_type_id' => ['The release type id field must be an integer.'],
                'technical_requirement_id' => ['The technical requirement id field must be an integer.'],
                'version' => ['The version field must be a string.'],
                'description' => ['The description field must be a string.'],
                'is_ready' => ['The is ready field must be true or false.'],
            ],
        ]);
    }

    /**
     * Тестирование валидации полей 'version' и 'description' при создании релиза, StoreRequest: на максимальную длину
     * @test
     */
    public function attributes_name_and_description_should_not_exceed_max_length()
    {
        $data = [
            'file' => $this->file,
            'project_id' => $this->project->id,
            'platform_id' => $this->platform->id,
            'change_id' => $this->change->id,
            'release_type_id' => $this->rt->id,
            'is_ready' => false,
            'technical_requirement_id' => $this->tech->id,
            'description' => 'a'.str_repeat('b', 255),
            'version' => '1'.str_repeat('0', 10)
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/releases', $data);
        $response->assertStatus(422)->assertJson([
            'errors' => [
                'version' => ['The version field must not be greater than 10 characters.'],
                'description' => ['The description field must not be greater than 255 characters.'],
            ],
        ]);
    }

    /**
     * Тестирование валидации полей с id при создании релиза, StoreRequest: на существование
     * @test
     */
    public function validation_checking_fields_for_existence()
    {
        $data = [
            'file' => $this->file,
            'project_id' => 2,
            'platform_id' => 3,
            'change_id' => 4,
            'release_type_id' => 5,
            'technical_requirement_id' => 6,
            'version' => fake()->randomNumber(1).'.'.fake()->randomNumber(1).'.'.fake()->randomNumber(1),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->userToken,
        ])->post('api/releases', $data);
        $response->assertStatus(422)->assertJson([
            'errors' => [
                'technical_requirement_id' => ['The selected technical requirement id is invalid.'],
                'release_type_id' => ['The selected release type id is invalid.'],
                'change_id' => ['The selected change id is invalid.'],
                'platform_id' => ['The selected platform id is invalid.'],
                'project_id' => ['The selected project id is invalid.'],
            ],
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
            'project_id' => $this->project->id,
            'platform_id' => $this->platform->id,
            'change_id' => $this->change->id,
            'description' => fake()->sentence(),
            'release_type_id' => $this->rt->id,
            'is_ready' => false,
            'technical_requirement_id' => $this->tech->id,
            'version' => fake()->randomNumber(1).'.'.fake()->randomNumber(1).'.'.fake()->randomNumber(1),
        ];
        $response = $this->post('api/releases', $data);

        $response->assertStatus(401);
    }
}

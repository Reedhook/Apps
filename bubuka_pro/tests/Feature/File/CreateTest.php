<?php

/** Тестирования метода create, контроллера CreateController of File */

namespace Tests\Feature\File;

use App\Models\File;
use App\Services\File\ValidationService;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Testing\File as TestingFile;
use App\Models\User;
use App\Services\File\FileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\File\CreateController as FileController;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    protected $userToken;
    protected TestingFile $file;
    protected FileController $FileCreate;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->userToken = JWTAuth::fromUser($user);

        Storage::fake('local');
        $this->file = TestingFile::create(fake()->word(), fake()->randomNumber());
        $this->FileCreate=new FileController(new FileService(), new ValidationService());
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
            'download_url' => fake()->word().'/'.fake()->word().'/'.fake()->word(),
            'version' =>
                fake()->randomNumber(1).'.'.
                fake()->randomNumber(1).'.'.
                fake()->randomNumber(1),
        ];
        $response = $this->FileCreate->save($data);
        $response_file = File::find($response['file_id']);
        $this->assertDatabaseCount('files', 1);
        $this->assertEquals($this->file->sizeToReport, $response_file['size']);
        $this->assertEquals($data['version'], $response_file['name']);
    }

    /**
     * Тестирование валидации на существование файла с таким же путем: на уникальность пути
     * @test
     * @throws Exception
     */
    public function validation_unique_path()
    {
        $this->expectException(HttpResponseException::class);

        $data = [
            'file' => $this->file,
            'download_url' => fake()->word().'/'.fake()->word().'/'.fake()->word(),
            'version' =>
                fake()->randomNumber(1).'.'.
                fake()->randomNumber(1).'.'.
                fake()->randomNumber(1),
        ];

        $this->FileCreate->save($data);

        $this->FileCreate->save($data);
    }


}

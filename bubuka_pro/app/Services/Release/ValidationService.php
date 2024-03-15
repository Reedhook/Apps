<?php

namespace App\Services\Release;

use App\DTO\ReleaseDTO;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class ValidationService
{
    /**
     * Метод для проверки существование связи между пользователем и проектом
     * @param $dto
     * @return void
     */
    public function existsProjectUser($dto):void
    {
        $project_id = $dto->project_id;
        $user_id = auth()->id();

        $response = DB::table('projects_users')
            ->where('user_id', $user_id)
            ->where('project_id', $project_id)
            ->exists();
        $response ?: throw new HttpResponseException(response()->json([
            'status' => false, 'errors' => 'Пользователь не включен в проект', 'code' => 422
        ], 422));
    }

    /**
     * Метод для проверки существование связи между платформой и проектом
     * @param  ReleaseDTO  $dto
     * @return void
     */
    public function existsProjectPlatform(ReleaseDTO $dto): void
    {
        $project_id = $dto->project_id;
        $platform_id = $dto->platform_id;

        $response = DB::table('projects_platforms')
            ->where('platform_id', $platform_id)
            ->where('project_id', $project_id)
            ->exists();
        $response ?: throw new HttpResponseException(response()->json([
            'status' => false, 'errors' => 'Платформа не включена в проект', 'code' => 422
        ], 422));
    }
}

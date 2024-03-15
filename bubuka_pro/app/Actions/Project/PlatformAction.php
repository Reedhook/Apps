<?php

namespace App\Actions\Project;

use App\Models\Platform;
use App\Models\Project;
use App\Repositories\IndexRepository;
use Exception;
use Illuminate\Support\Facades\Log;

class PlatformAction
{
    protected IndexRepository $indexRepository;

    public function __construct(IndexRepository $indexRepository)
    {
        $this->indexRepository = $indexRepository;
    }

    /**
     * @throws Exception
     */
    public function execute($project_id, $platform_id, $method): array
    {
        /** Поиск записей по id, в Случае ошибки выкинет исключение 404 */
        $project = $this->indexRepository->getByObject(new Project(), $project_id);
        $platform = $this->indexRepository->getByObject(new Platform(), $platform_id);
        $message = null;

        /** Проверка на существование связи между проектом и пользователем */
        $result = $project->platforms()->where('platform_id', $platform['id'])->exists();

        /** Проверяем сперва метод */
        if ($method == 'PATCH') {
            /** Проверка на существование связи */
            !$result ?: throw new Exception('Данная платформа уже добавлена к проекту', 400);

            /** Добавляем пользователя к проекту */
            $project->platforms()->attach($platform);
            $message = 'Платформа была добавлена к проекту';
        } elseif ($method == 'DELETE') {
            /** Проверка на существование связи */
            $result ?: throw new Exception('Данной платформы нет в проекте', 400);

            /** Удаляем пользователя из проекта */
            $project->platforms()->detach($platform);
            $message = 'Платформа удалена из проекта';
        }
        Log::channel('project')
            ->info(
                message: $message,
                context: [
                    'project_id' => $project['id'],
                    'platform_id' => $platform['id'],
                ]
            );
        $response = $this->indexRepository->getByObjectWith(new Project(), $project_id, ['platforms', 'users']);
        return [
            'response' => $response,
            'message' => $message
        ];
    }
}

<?php

namespace App\Actions\Project;

use App\Models\Project;
use App\Models\User;
use App\Repositories\IndexRepository;
use Exception;
use Illuminate\Support\Facades\Log;

class UserAction
{
    protected IndexRepository $indexRepository;
    public function __construct(IndexRepository $indexRepository)
    {
        $this->indexRepository= $indexRepository;
    }

    /**
     * @throws Exception
     */
    public function execute(int $project_id, int $user_id, string $method)
    {
        /** Поиск записей по id, в Случае ошибки выкинет исключение 404 */
        $project = $this->indexRepository->getByObject(new Project(), $project_id);
        $user = $this->indexRepository->getByObject(new User(), $user_id);
        $message = null;

        /** Проверка на существование связи между проектом и пользователем */
        $result = $project->platforms()->where('platform_id', $user['id'])->exists();

        /** Проверяем сперва метод */
        if ($method == 'PATCH') {
            // Проверка на существовании связи
            $result = $project->users()->where('user_id', $user['id'])->exists();
            !$result ?: throw new Exception('Данный пользователь уже добавлен к проекту', 400);

            /** Добавляем пользователя к проекту */
            $project->users()->attach($user);
            $message = 'К проекту добавлен пользователь';
        } elseif ($method == 'DELETE') {
            // Проверка на существовании связи
            $result = $project->users()->where('user_id', $user['id'])->exists();
            /** Проверка на существование связи */
            $result ?: throw new Exception('Данного пользователя нет в проекте', 400);

            /** Удаляем пользователя из проекта */
            $project->users()->detach($user);
            $message = 'Из проекта удален пользователь';
        }
        Log::channel('project')
            ->info(
                message: $message,
                context: [
                    'project_id' => $project['id'],
                    'platform_id' => $user['id'],
                ]
            );
        $response = $this->indexRepository->getByObjectWith(new Project(), $project_id, ['platforms', 'users']);
        return [
            'response' => $response,
            'message' => $message
        ];
    }
}

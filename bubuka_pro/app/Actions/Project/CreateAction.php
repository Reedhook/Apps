<?php

namespace App\Actions\Project;

use App\DTO\ProjectDTO;
use App\Models\Project;
use App\Services\DbService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CreateAction
{
    protected DbService $createService;
    protected Model $model;
    protected UserAction $addUserAction;

    public function __construct(DbService $createService, UserAction $addUserAction)
    {
        $this->createService = $createService;
        $this->model = new Project();
        $this->addUserAction = $addUserAction;
    }

    public function execute(ProjectDTO $dto): Model
    {
        $project = $this->createService->create($this->model, $dto); // отправляем на сервис для создания записи
        $this->addUserAction->execute($project['id'], $dto->admin_id, 'PATCH'); // сразу записываем в участники проекта, админа создавшего проект

        /** Записываем информацию о созданном проекте в логи */
        Log::channel('project')
            ->info(
                message: ' Был создан проект: ',
                context: [$project]
            );
        return $project;
    }
}

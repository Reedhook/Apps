<?php

namespace App\Actions\Project;

use App\Models\Project;
use App\Services\DbService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class DeleteAction
{
    protected DbService $dbService;
    protected Model $model;

    public function __construct(DbService $dbService)
    {
        $this->dbService = $dbService;
        $this->model = new Project();
    }

    public function execute(int $id): void
    {
        // Отправляем в сервис для удаления
        $project = $this->dbService->delete($this->model, $id);

        /** Перед удалением проверяем есть ли связи в связующей таблицу */
        if ($project->platforms()->exists()) {

            /** Если связь существует, то удаляем абсолютно все связи */
            $platforms = $project->platforms()->get();
            foreach ($platforms as $platform) {
                $project->platforms()->detach($platform);
            }
        }

        /** Логирование результата */
        Log::channel('change')
            ->info(
                message: 'Запись Project была удалена',
                context: [$project]
            );
    }
}

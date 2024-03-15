<?php

namespace App\Actions\Platform;

use App\Models\Platform;
use App\Repositories\IndexRepository;
use App\Services\DbService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class DeleteAction
{
    protected DbService $dbService;
    protected Model $model;
    protected IndexRepository $indexRepository;

    public function __construct(DbService $dbService, IndexRepository $indexRepository)
    {
        $this->dbService = $dbService;
        $this->model = new Platform();
        $this->indexRepository = $indexRepository;
    }

    public function execute(int $id): void
    {
        // Отправляем в сервис для удаления
        $platform = $this->indexRepository->getByObject($this->model, $id);

        /** Перед удалением проверяем есть ли связи в связующей таблице с таблицей projects */
        if ($platform->projects()->exists()) {

            /** Если связь существует, то удаляем абсолютно все связи */
            $projects = $platform->projects()->get();
            foreach ($projects as $project) {
                $platform->projects()->detach($project);
            }
        }
        $platform->delete();

        /** Логирование результата */
        Log::channel('platform')
            ->info(
                message: ' Запись удалена ',
                context: [$platform]
            );
    }
}

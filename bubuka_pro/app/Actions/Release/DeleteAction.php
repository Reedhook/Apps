<?php

namespace App\Actions\Release;

use App\Models\Release;
use App\Repositories\IndexRepository;
use App\Services\DbService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class DeleteAction
{
    protected DbService $dbService;
    protected Model $model;
    protected IndexRepository $indexRepository;
    private \App\Actions\File\DeleteAction $fileAction;

    public function __construct(DbService $dbService, IndexRepository $indexRepository, \App\Actions\File\DeleteAction $fileAction)
    {
        $this->dbService = $dbService;
        $this->model = new Release();
        $this->indexRepository = $indexRepository;
        $this->fileAction = $fileAction;
    }

    public function execute(int $id): void
    {
        // Отправляем в сервис для удаления
        $release = $this->indexRepository->getByObject($this->model, $id);
        $this->fileAction->execute($release['file_id']);
        $release = $this->dbService->delete($this->model, $id);

        /** Логирование результата */
        Log::channel('release')
            ->info(
                message: ' Запись удалена ',
                context: [$release]
            );
    }
}

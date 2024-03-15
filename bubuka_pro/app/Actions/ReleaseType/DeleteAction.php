<?php

namespace App\Actions\ReleaseType;

use App\Models\ReleaseType;
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
        $this->model = new ReleaseType();
        $this->indexRepository = $indexRepository;
    }

    public function execute(int $id): void
    {
        $rt = $this->dbService->delete($this->model, $id);

        /** Логирование результата */
        Log::channel('release_type')
            ->info(
                message: ' Запись удалена ',
                context: [$rt]
            );

    }
}

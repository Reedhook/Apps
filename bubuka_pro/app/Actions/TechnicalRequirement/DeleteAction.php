<?php

namespace App\Actions\TechnicalRequirement;

use App\Models\TechnicalRequirement;
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
        $this->model = new TechnicalRequirement();
    }

    public function execute(int $id): void
    {
        // Отправляем в сервис для удаления
        $tech = $this->dbService->delete($this->model, $id);

        /** Логирование результата */
        Log::channel('tech_req')
            ->info(
                message: ' Запись удалена ',
                context: [$tech]
            );

    }
}

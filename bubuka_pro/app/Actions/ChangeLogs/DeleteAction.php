<?php

namespace App\Actions\ChangeLogs;

use App\Models\ChangeLog;
use App\Services\DbService;
use Illuminate\Support\Facades\Log;

class DeleteAction
{
    protected DbService $dbService;
    protected ChangeLog $model;

    public function __construct(DbService $dbService)
    {
        $this->dbService = $dbService;
        $this->model = new ChangeLog();
    }

    public function execute(int $id): void
    {
        // Отправляем в сервис для удаления
        $changelog = $this->dbService->delete($this->model, $id);

        /** Логирование результата */
        Log::channel('change')
            ->info(
                message: 'Запись ChangeLog была удалена',
                context: [$changelog]
            );
    }
}

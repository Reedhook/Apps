<?php

namespace App\Actions\ChangeLogs;

use App\DTO\ChangeLogDTO;
use App\Models\ChangeLog;
use App\Services\DbService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CreateAction
{
    protected DbService $createService;
    protected Model $model;

    public function __construct(DbService $createService)
    {
        $this->createService = $createService;
        $this->model = new ChangeLog();
    }

    public function execute(ChangeLogDTO $changelogDTO): Model
    {
        $changelog = $this->createService->create($this->model, $changelogDTO); // отправляем на сервис для создания записи

        /** Логирование */
        Log::channel('change')
            ->info(
                message: 'Была создана запись по изменениям ChangeLog',
                context: [$changelog]
            );
        return $changelog;
    }
}

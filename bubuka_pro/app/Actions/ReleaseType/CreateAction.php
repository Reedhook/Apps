<?php

namespace App\Actions\ReleaseType;

use App\DTO\ReleaseTypeDTO;
use App\Models\ReleaseType;
use App\Services\DbService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CreateAction
{
    protected DbService $dbService;
    protected Model $model;

    public function __construct(DbService $dbService)
    {
        $this->dbService = $dbService;
        $this->model = new ReleaseType();
    }

    public function execute(ReleaseTypeDTO $dto): Model
    {
        $rt = $this->dbService->create($this->model, $dto);

        /** Логирование результата */
        Log::channel('release_type')
            ->info(
                message: ' Был добавлен тип релиза ',
                context: [$rt]
            );

        return $rt;
    }
}

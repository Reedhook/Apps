<?php

namespace App\Actions\Platform;

use App\DTO\PlatformDTO;
use App\Models\Platform;
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
        $this->model = new Platform();
    }

    public function execute(PlatformDTO $dto): Model
    {
        $platform = $this->dbService->create($this->model, $dto);
        Log::channel('platform')
            ->info(
                message: 'Была добавлена новая платформа ',
                context: [$platform]
            );
        return $platform;
    }
}

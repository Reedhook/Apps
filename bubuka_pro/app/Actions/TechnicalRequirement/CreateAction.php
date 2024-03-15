<?php

namespace App\Actions\TechnicalRequirement;

use App\DTO\ReleaseTypeDTO;
use App\DTO\TechnicalRequirementDTO;
use App\Models\ReleaseType;
use App\Models\TechnicalRequirement;
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
        $this->model = new TechnicalRequirement();
    }

    public function execute(TechnicalRequirementDTO $dto): Model
    {
        $tech = $this->dbService->create($this->model, $dto);

        /** Логирование результата */
        Log::channel('tech_req')
            ->info(
                message: ' Была добавлена техническая характеристика ',
                context: [$tech]
            );

        return $tech;
    }
}

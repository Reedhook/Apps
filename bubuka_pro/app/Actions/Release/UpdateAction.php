<?php

namespace App\Actions\Release;

use App\DTO\PlatformDTO;
use App\DTO\ReleaseDTO;
use App\Exceptions\SameDataException;
use App\Models\Platform;
use App\Models\Release;
use App\Repositories\IndexRepository;
use App\Services\DbService;
use App\Services\Release\ReleaseService;
use App\Services\Release\ValidationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class UpdateAction
{
    protected Model $model;
    protected IndexRepository $indexRepository;
    protected DbService $dbService;
    private ValidationService $validationService;
    private ReleaseService $releaseService;
    private \App\Actions\File\UpdateAction $fileAction;

    public function __construct(
        IndexRepository $indexRepository,
        DbService $dbService,
        ValidationService $validationService,
        ReleaseService $releaseService,
        \App\Actions\File\UpdateAction $fileAction,
    ) {
        $this->indexRepository = $indexRepository;
        $this->model = new Release();
        $this->dbService = $dbService;
        $this->validationService = $validationService;
        $this->releaseService = $releaseService;
        $this->fileAction = $fileAction;
    }

    public function execute(ReleaseDTO $dto, int $id)
    {
        $release = $this->indexRepository->getByObject($this->model, $id);

        // Поля доступные для изменений
        $fieldsToCheck = [
            'change_id', 'platform_id', 'description', 'release_type_id', 'is_ready',
            'technical_requirement_id', 'version', 'file'
        ];

        // Поля, которые могут изменить расположение файла
        $fieldsToCheck_file = [
            'version', 'release_type_id', 'platform_id', 'file'
        ];

        foreach ($fieldsToCheck as $field) {
            if ($dto->$field != null) {
                // меняем данные, если данные не равны старым данным и это не поля, меняющие расположение файла
                if (!in_array($field, $fieldsToCheck_file) && $dto->$field != $release[$field]) {
                    $release[$field] = $dto->$field;
                } elseif (in_array($field, $fieldsToCheck_file)) {

                    //Если platform_id, то проверяем есть ли связки между новой платформой и проектом
                    if ($field === "platform_id") {
                        $dto->project_id = $release['project_id'];
                        $this->validationService->existsProjectPlatform($dto);
                    }
                    $release[$field] = $dto->$field;
                }
            }
        }

        // Получение атрибутов модели
        $releaseAttributes = $release->getAttributes();

        foreach ($releaseAttributes as $field => $value) {
            if (property_exists($dto, $field)) {
                $dto->$field = $value;
            }
        }

        // Проверяем наличие метода download_url в releaseService
        if (method_exists($this->releaseService, 'download_url')) {
            $this->releaseService->download_url($dto);
        }
        // Меняем данные файла
        $this->fileAction->execute($dto, $dto->file_id);
        //Меняем записи релиза
        $this->dbService->update($this->model, $id, $dto);
        return $this->indexRepository->getByObject(new Release(), $id);
    }
}

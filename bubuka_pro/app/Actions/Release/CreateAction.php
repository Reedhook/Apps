<?php

namespace App\Actions\Release;

use App\DTO\ReleaseDTO;
use App\Models\Release;
use App\Repositories\IndexRepository;
use App\Services\DbService;
use App\Services\Release\ReleaseService;
use App\Services\Release\ValidationService;
use Illuminate\Database\Eloquent\Model;

class CreateAction
{
    public ValidationService $validationService;
    private ReleaseService $releaseService;
    private \App\Actions\File\CreateAction $fileAction;
    private DbService $dbService;
    public Model $model;
    private IndexRepository $indexRepository;

    public function __construct(
        ValidationService $validationService,
        ReleaseService $releaseService,
        \App\Actions\File\CreateAction $fileAction,
        DbService $dbService,
        IndexRepository $indexRepository
    ) {
        $this->validationService = $validationService;
        $this->releaseService = $releaseService;
        $this->fileAction = $fileAction;
        $this->dbService = $dbService;
        $this->model = new Release();
        $this->indexRepository = $indexRepository;
    }

    public function execute(ReleaseDTO $dto)
    {
        // Дополнительная валидация данных
        $this->validationService->existsProjectUser($dto);
        $this->validationService->existsProjectPlatform($dto);

        // создаем путь для загрузки файла
        $this->releaseService->download_url($dto);

        // Работу с файлами отправляем на другой Action
        $this->fileAction->execute($dto);

        // Создаем запись Release
        $release = $this->dbService->create($this->model, $dto);

        // Возвращаем ответ
        return $this->indexRepository->getByObject($this->model, $release['id']);
    }
}

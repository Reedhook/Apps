<?php

namespace App\Actions\File;

use App\DTO\PlatformDTO;
use App\DTO\ReleaseDTO;
use App\Exceptions\SameDataException;
use App\Models\File;
use App\Models\Platform;
use App\Repositories\IndexRepository;
use App\Services\DbService;
use App\Services\File\FileService;
use App\Services\File\ValidationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class UpdateAction
{
    protected Model $model;
    protected IndexRepository $indexRepository;
    protected DbService $dbService;
    private ValidationService $validationService;
    private FileService $fileService;

    public function __construct(IndexRepository $indexRepository, DbService $dbService, ValidationService $validationService, FileService $fileService)
    {
        $this->indexRepository = $indexRepository;
        $this->model = new File();
        $this->dbService = $dbService;
        $this->validationService = $validationService;
        $this->fileService = $fileService;
    }

    public function execute(ReleaseDTO $dto, int $id)
    {
        $file = $this->indexRepository->getByObject($this->model, $id);
        /** Сохраняем старые данные для логов */
        $oldData = $file->getAttributes();

        /** Создание пути до файла */
        $file['path'] = $this->validationService->file_path($dto, $file);

        /** Если изменен сам файл или путь к нему*/
        if (isset($dto->file)) {
            $this->fileService->update_file($dto, $file, $oldData);
        } /** Если изменен только путь к файлу */
        elseif ($file['path'] != $oldData['path']) {
            $this->fileService->move_file($oldData, $file);
        }

        /** Создаем url для скачивания */
        $dto->download_url = url("download/$dto->download_url/$dto->version");

        /** Проверяем изменилась ли модель файла */
        if ($file->isDirty()) {
            $this->dbService->update($this->model, $dto->file_id, $file);

            // Флажок нужен, чтобы определять были ли изменения в файле
            $dto->file_status = true;
        }
    }
}

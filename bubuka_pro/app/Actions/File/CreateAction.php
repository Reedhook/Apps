<?php

namespace App\Actions\File;

use App\DTO\ReleaseDTO;
use App\Models\File;
use App\Services\DbService;
use App\Services\File\FileService;
use App\Services\File\ValidationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CreateAction
{
    private ValidationService $validationService;
    private FileService $fileService;
    private DbService $dbService;
    public Model $model;

    public function __construct(ValidationService $validationService, FileService $fileService, DbService $dbService)
    {
        $this->validationService = $validationService;
        $this->fileService = $fileService;
        $this->dbService = $dbService;
        $this->model = new File();
    }

    public function execute(ReleaseDTO $dto)
    {
        /** Проверка на существование файла с таким путем */
        $file = $this->validationService->validationFile($dto);

        /** Сохранение файла в storage  */
        $this->fileService->add_file($dto, $file);

        /** Путь к файлу по которому можно скачать файл */
        $dto->download_url = url("download/$dto->download_url/$dto->version");

        $fileModel = $this->dbService->createArr($this->model, $file);

        Log::channel('file')
            ->info(
                message: 'Файл был сохранен и была создана запись',
                context: [$fileModel]
            );
        $dto->file_id = $fileModel['id'];
    }
}

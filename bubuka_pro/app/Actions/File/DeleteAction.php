<?php

namespace App\Actions\File;

use App\Models\File;
use App\Repositories\IndexRepository;
use App\Services\DbService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteAction
{
    protected DbService $dbService;
    protected Model $model;
    protected IndexRepository $indexRepository;

    public function __construct(DbService $dbService, IndexRepository $indexRepository)
    {
        $this->dbService = $dbService;
        $this->model = new File();
        $this->indexRepository = $indexRepository;
    }

    /**
     * @throws Exception
     */
    public function execute(int $id): void
    {
        // Для удаления самого файла получаем путь к нему
        $file = $this->indexRepository->getByObject($this->model, $id);

        /** Удаление существующего файла */
        Storage::exists($file['path']) ?
            Storage::delete($file['path']) :
            throw new Exception('Файла по такому пути не существует', 404);

        // Удаляем запись в бд
        $this->dbService->delete($this->model, $id);

        /** Логирование результата */
        Log::channel('file')
            ->info(
                message: ' Запись и файл удалены ',
                context: [$file]
            );
    }
}

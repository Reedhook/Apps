<?php

namespace App\Services\File;

use App\DTO\ReleaseDTO;
use App\Http\Controllers\ReleaseDownload\CreateController as ReleaseDownloadController;
use App\Models\Release;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileService
{


    /**
     * Метод для обновления данных в бд
     * @param  Model  $file
     * @param  array  $oldData
     * @return void
     */
    public function update(Model $file, array $oldData): void
    {
        /** Обновление записей */
        $file->save();

        /** Достаем изменения в модели и логируем */
        $newData = $file->getChanges();
        foreach ($newData as $field => $value) {

            // Логируем
            Log::channel('file')
                ->info(
                    message: ' Запись была изменена ',
                    context: [
                        'file_id' => $file['id'],
                        'field' => $field,
                        'Старое значение' => $oldData[$field],
                        'Новое значение' => $value
                    ]
                );
        }
    }

    /**
     * Метод для обновления файла
     * @param  ReleaseDTO  $dto
     * @param  Model       $file
     * @param  array       $oldData
     * @return void
     * @throws Exception
     */
    public function update_file(ReleaseDTO $dto, Model $file, array $oldData): void
    {
        $validation = new ValidationService();

        /** Получаем данные о файле */
        $arr_file = $validation->file_info($dto);
        foreach ($arr_file as $key => $key_value) {

            /** Проверяем на изменения в данных */
            if ($file[$key] != $key_value) {
                /** Обновляем данные */
                $file[$key] = $key_value;
            }
        }
        if ($file->isDirty()) {
            Log::info($file['path']);
            Log::info($oldData['path']);
            /** Удаление существующего файла */
            Storage::exists($oldData['path']) ?
                Storage::delete($oldData['path']) :
                throw new Exception('Файла по такому пути не существует', 404);
            /** Создание нового пути до файла */
            $file['path'] = $validation->file_path($dto, $file);

            /** Добавление нового файла */
            Storage::putFileAs("public/$dto->download_url", $dto->file,
                "$dto->version.$file[extension]") ?:
                throw new Exception('Добавление файла не удалось', 400);
            $dto->file_status = true;
        } else {
            $dto->file_status = false;
        }
    }

    /**
     * Метод для логирования скачивания
     * @param  Request  $request
     * @param           $file
     * @return void
     * @throws Exception
     */
    public function logging_ghost(Request $request, $file): void
    {
        $release_download = new ReleaseDownloadController();
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');
        $utm['Source'] = $request->query('utm_source');
        $utm['Medium'] = $request->query('utm_medium');
        $utm['Campaign'] = $request->query('utm_campaign');
        /** Ищем id релиза */
        $releaseId = Release::where('file_id', $file['id'])->value('id');
        $release_download->store($ip, $userAgent, $utm, $releaseId);
    }

    /**
     * Метод для определения самой новой версии из существующих
     * @param $versions
     * @return false|mixed
     */
    public function version($versions): mixed
    {
        /** Сортируем в порядке убывания */
        usort($versions, function ($a, $b) {
            return version_compare($b, $a);
        });
        /** Получаем самую новую версию  */
        return reset($versions);
    }

    /**
     * Метод для скачивания файлов
     * @param  string  $version
     * @param  array   $extensions
     * @param  string  $project
     * @param  string  $platform
     * @param  string  $type
     * @return BinaryFileResponse
     * @throws Exception
     */
    public function download(
        string $version,
        array $extensions,
        string $project,
        string $platform,
        string $type
    ): BinaryFileResponse {
        /** Объединяем название и расширение */
        $file = $version.'.'.$extensions[$version];

        /** Получаем полный путь к файлу */
        $fullPath = ("public/$project/$platform/$type/$file");

        /**  Название файла при скачивании */
        $nameFile = $project.'_'.$platform.'_'.$type.'_'.$file;

        if (Storage::exists($fullPath)) {
            /** Принудительное скачивание при обращении по данному endpoint-у */
            return response()->download(storage_path('app/'.$fullPath), $nameFile);
        } else {
            throw new Exception('Файла с таким названием не найдено', 404);
        }
    }

    /**
     * Метод для добавления файлов в storage
     * @param  ReleaseDTO  $dto
     * @param  array       $file
     * @return void
     * @throws Exception
     */
    public function add_file(ReleaseDTO $dto, array $file): void
    {
        /** Добавление нового файла */
        Storage::putFileAs("public/$dto->download_url", $dto->file,
            "$dto->version.$file[extension]") ?: throw new Exception('Добавление файла не удалось',
            400);
    }

    /**
     * Метод для переноса файла из одной директории в другую
     * @param  array  $oldData
     * @param  Model  $file
     * @return void
     * @throws Exception
     */
    public function move_file(array $oldData, Model $file): void
    {
        Log::info($file['path']);
        Log::info($oldData['path']);
        Storage::exists($file['path']) ? Storage::move($file['path'],
            $file['path']) : throw new Exception('Файла по такому пути не существует',
            404);
    }
}

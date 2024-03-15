<?php

namespace App\Http\Controllers\File;

use Illuminate\Support\Facades\Storage;

class DeleteController extends BaseFileController
{
//    /**
//     *  Метод для удаления записи
//     * @param  int           $id
//     * @param  DeleteAction  $action
//     * @return JsonResponse
//     * @throws Exception
//     */
//    public function delete(int $id, DeleteAction $action): JsonResponse
//    {
//        $action->execute($id);
//
//        return $this->deleteResponse(); // отправка json ответа
//    }

    /**
     * Метод для удаления пустых директории в Storage, запускается через планировщик
     * @param  string  $directory
     * @return void
     */
    public function deleteEmptyDirectories(string $directory): void
    {
        $directories = Storage::directories($directory);

        foreach ($directories as $subdirectory) {
            $files = Storage::files($subdirectory);
            $subDirectories = Storage::directories($subdirectory);

            if (count($files) === 0 && count($subDirectories) === 0) {
                // Директория пустая, удаляем ее
                Storage::deleteDirectory($subdirectory);
            } else {
                // Рекурсивно вызываем deleteEmptyDirectories для поддиректории
                $this->deleteEmptyDirectories($subdirectory);
            }
        }

        if ($directory !== '/') {
            // После проверки всех поддиректорий, проверяем текущую директорию
            $files = Storage::files($directory);
            $subDirectories = Storage::directories($directory);

            if (count($files) === 0 && count($subDirectories) === 0) {
                // Директория пустая, удаляем ее
                Storage::deleteDirectory($directory);
            }
        }    }
}


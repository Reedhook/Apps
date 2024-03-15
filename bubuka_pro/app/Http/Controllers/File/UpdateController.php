<?php

namespace App\Http\Controllers\File;

use App\Models\File;
use Exception;
use Illuminate\Database\Eloquent\Model;

class UpdateController extends BaseFileController
{
    /**
     * Обновление записей сущности Platform после валидации данных
     *
     * @param  Model  $release
     * @return Model
     * @throws Exception
     */
    public function update(Model $release): Model
    {
        /** Поиск записи по id в Случае ошибки выкинет исключение 404 */
        $file = File::findOrFail($release['file_id']);

        /** Сохраняем старые данные для логов */
        $oldData = $file->getAttributes();

        /** Создание пути до файла */
        $file['path'] = $this->validation->file_path($release, $file);

        /** Если изменен сам файл или путь к нему*/
        if (isset($release['file'])) {
            $this->file->update_file($release, $file, $oldData);
        } /** Если изменен только путь к файлу */
        elseif ($file['path'] != $oldData['path']) {
            $this->file->move_file($oldData, $file);
        }

        /** Создаем url для скачивания */
        $release['download_url'] = url("download/$release[download_url]/$release[version]");

        /** Проверяем изменилась ли модель файла */
        if ($file->isDirty()) {
            $this->file->update($file, $oldData);

            // Флажок нужен, чтобы определять были ли изменения в файле
            $release['status'] = true;
        }
        $release['file'] = $file;
        return $release;
    }

}

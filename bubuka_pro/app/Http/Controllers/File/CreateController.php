<?php

namespace App\Http\Controllers\File;

class CreateController extends BaseFileController
{

//    /**
//     * Сохранение файлов и добавление метаданных в базу данных
//     *
//     * @param  ReleaseDTO  $dto
//     * @return array
//     * @throws Exception
//     */
//    public function save(ReleaseDTO $dto): array
//    {
//        /** Проверка на существование файла с таким путем */
//        $file = $this->validation->validation($data);
//
//        /** Сохранение файла в storage  */
//        $this->file->add_file($data, $file);
//
//        /** Путь к файлу по которому можно скачать файл */
//        $data['download_url'] = url("download/$data[download_url]/$data[version]");
//
//        /** Отправляем сохранение в бд в сервисы */
//        return $this->file->store($file, $data);
//    }
}

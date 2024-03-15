<?php

namespace App\Services\File;

use App\DTO\ReleaseDTO;
use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidationService
{
    /**
     * Метод для кастомной валидации
     * @param  ReleaseDTO  $dto
     * @return array
     */
    public function validationFile(ReleaseDTO $dto): array
    {
        /** Сбор данных о файле */
        $file = $this->file_info($dto);

        /** Создание пути до файла */
        $file['path'] = $this->file_path($dto, $file);
        !File::where('path', $file['path'])->exists() ?: throw new HttpResponseException(response(['По данному пути файл уже существует'],404));
        return $file;
    }

    /**
     * Метод для сбора информации о файле
     *
     * @param  ReleaseDTO  $dto
     * @return array
     */
    public function file_info(ReleaseDTO $dto): array
    {
        /** Получение имени, расширения и размера файла */
        $file['extension'] = $dto->file->getClientOriginalExtension();

        /** Читаем содержимое файла */
        $contents = file_get_contents($dto->file);

        /** Инициализируем расширение FileInfo */
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        /** Получаем MIME-тип файла */
        $file['mime_type'] = finfo_buffer($finfo, $contents);

        /** Закрываем расширение FileInfo */
        finfo_close($finfo);

        $file['name'] = $dto->version;
        $file['size'] = $dto->file->getSize();
        return $file;
    }

    /**
     * Метод для формирования пути до файла
     * @param  ReleaseDTO   $dto
     * @param  array|Model  $file
     * @return string
     */
    public function file_path(ReleaseDTO $dto , array|Model $file): string
    {
        return "public/$dto->download_url/$dto->version.$file[extension]";
    }

}

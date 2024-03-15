<?php

namespace App\Http\Controllers\File;

use App\DTO\IndexDTO;
use App\Http\Requests\File\IndexRequest;
use App\Models\File;
use App\Repositories\IndexRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class IndexController extends BaseFileController
{
    /**
     * @OA\Get(
     *     path="/api/files/",
     *     summary="Получение записей о всех файлах",
     *     tags={"Files"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="Кол-во записей на странице",
     *          @OA\Schema(
     *              type="integer",
     *              example=20
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="offset",
     *          in="query",
     *          description="Кол-во пропусков",
     *          @OA\Schema(
     *              type="integer",
     *              example=10
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent( ref="#/components/schemas/File" )
     *     ),
     *     @OA\Response (
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent ( ref="#/components/schemas/401" )
     *     )
     * )
     *
     * Получение записей всех платформы
     *
     * @param  IndexRequest     $request
     * @param  IndexRepository  $indexRepository
     * @return JsonResponse
     */
    public function index(IndexRequest $request, IndexRepository $indexRepository): JsonResponse
    {
        $dto = IndexDTO::fromRequest($request);

        $files = $indexRepository->all(new File(), $dto);

        /** Возвращаем результат в виде коллекции */
        return $this->OkResponse($files, 'files');
    }

    /**
     * @OA\Get(
     *     path="/api/files/{file_id}/",
     *     summary="Получение записи файла",
     *     tags={"Files"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="file_id",
     *          in="path",
     *          required=true,
     *          description="Id платформы",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *              example=3
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent( ref="#/components/schemas/File" )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(ref="#/components/schemas/404")
     *     ),
     *     @OA\Response (
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent ( ref="#/components/schemas/401" )
     *     )
     *  )
     *  Получение записи Файла
     *
     * @param  int              $id
     * @param  IndexRepository  $indexRepository
     * @return JsonResponse
     */
    public function show(int $id, IndexRepository $indexRepository): JsonResponse
    {
        $file = $indexRepository->getByObject(new File(), $id);
        /** Поиск записи по id в Случае ошибки выкинет исключение 404 */
        return $this->OkResponse($file, 'file');
    }

    /**
     * @OA\Get(
     *     path="/download/{project}/{platform}/{type}/{version}/",
     *     summary="Получение записи файла",
     *     tags={"Files"},
     *     @OA\Parameter(
     *          name="project",
     *          in="path",
     *          required=true,
     *          description="Название проекта",
     *          @OA\Schema(
     *              type="string",
     *              example="Мобильная_разработка"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="platform",
     *          in="path",
     *          required=true,
     *          description="Название платформы",
     *          @OA\Schema(
     *              type="string",
     *              example="Android"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="type",
     *          in="path",
     *          required=true,
     *          description="Название типа релиза",
     *          @OA\Schema(
     *              type="string",
     *              example="Major_Release"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="version",
     *          in="path",
     *          required=true,
     *          description="Версия",
     *          @OA\Schema(
     *              type="string",
     *              example="1.0.0"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\MediaType(
     *              mediaType="application/octet-stream",
     *              @OA\Schema(
     *                  type="string",
     *                  format="binary"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent( ref="#/components/schemas/404" )
     *     )
     *  ),
     *  Скачивание установочного файла
     * @param  string  $project
     * @param  string  $platform
     * @param  string  $type
     * @param  string  $version
     * @param  Request  $request
     * @return BinaryFileResponse
     * @throws Exception
     */
    public function download(
        string $project,
        string $platform,
        string $type,
        string $version,
        Request $request
    ): BinaryFileResponse {
        $versions = [];
        $extensions = [];
        /** Получаем массив со всеми существующими файлами */
        $files = Storage::allFiles("public/$project/$platform/$type");

        foreach ($files as $file) {

            /** Поиск файла с таким путем в базе данных */
            $Model_file = File::where('path', $file)->first();

            /** Получаем имя и расширение файла и записываем в массив для дальнейшего использования */
            $versions[] = $Model_file['name'];
            $extensions[$Model_file['name']] = $Model_file['extension'];

            /** Логирование скачивания */
            $this->file->logging_ghost($request, $Model_file);
        }
        /** Если просят последнюю версию */
        ($version === 'latest') ?
            ($version = $this->file->version($versions)) :
            (array_key_exists($version, $extensions) ?:
                throw new Exception('Такой версии не существует', 404));

        /** Скачивание файла */
        return $this->file->download($version, $extensions, $project, $platform, $type);
    }
}

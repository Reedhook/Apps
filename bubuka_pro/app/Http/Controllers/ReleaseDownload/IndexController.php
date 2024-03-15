<?php

namespace App\Http\Controllers\ReleaseDownload;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReleaseDownload\IndexRequest;
use App\Models\ReleaseDownload;
use Illuminate\Http\JsonResponse;

class IndexController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/rels_dls/",
     *     summary="Получение записей о скачавших пользователях",
     *     tags={"Releases_Downloads"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="Кол-во записей на странице",
     *          style="form",
     *          explode=true,
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
     *          @OA\JsonContent( ref="#/components/schemas/Release_Download" )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent( ref="#/components/schemas/401" )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation Error",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  description="Статус кода",
     *                  example=false
     *              ),
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  description="Сообщение ошибки",
     *                  @OA\Property(
     *                      property="limit",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля limit",
     *                      example={
     *                          "The limit field is required when offset are present.",
     *                          "The limit field must be a integer"
     *                      }
     *                  ),
     *                  @OA\Property(
     *                      property="offset",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля offset",
     *                      example= "The offset field must be a integer"
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  description="Код ответа",
     *                  example=422
     *              )
     *          )
     *     )
     *
     * )
     *  Получить данные о всех технических характеристиках
     *
     * @param  IndexRequest  $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        /** Получаем все данные из запроса */
        $data = $request->validated();

        /** Раздельно добавляем условия */
        $query = ReleaseDownload::query();

        /** Проверка на существования условия "ограничение по количеству записей"*/
        if (isset($data['limit'])) {
            $query->limit($data['limit']);

            //Offset нельзя использовать без limit
            if (isset($data['offset'])) {
                $query->offset($data['offset']); // сколько записей нужно пропустить
            }
        }
        /** Возвращаем результат в виде коллекции */
        return $this->OkResponse($query->get(), 'releases_downloads');
    }


    /**
     * @OA\Get(
     *        path="/api/rels_dls/{rel_dl_id}/",
     *        summary="Получение записи",
     *        tags={"Releases_Downloads"},
     *        security={{"bearer_token":{}}},
     *        @OA\Parameter(
     *            name="rel_dl_id",
     *            in="path",
     *            description="Id записи скаченного релиза",
     *            required=true,
     *            @OA\Schema(
     *                type="integer",
     *                format="int64",
     *                example=3
     *            )
     *        ),
     *        @OA\Response(
     *            response=200,
     *            description="OK",
     *            @OA\JsonContent( ref="#/components/schemas/Release_Download" )
     *        ),
     *        @OA\Response(
     *            response=404,
     *            description="Not Found",
     *            @OA\JsonContent( ref="#/components/schemas/404" )
     *        ),
     *   )
     *  Получение записи
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        /** Поиск записи по id и подгружаем связанные платформы, в Случае ошибки выкинет исключение 404 */
        return $this->OkResponse(ReleaseDownload::findOrFail($id), 'release_download');
    }
}

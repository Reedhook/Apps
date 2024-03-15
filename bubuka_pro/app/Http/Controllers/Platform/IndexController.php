<?php

namespace App\Http\Controllers\Platform;

use App\DTO\IndexDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\IndexRequest;
use App\Models\Platform;
use App\Repositories\IndexRepository;
use Illuminate\Http\JsonResponse;

class IndexController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/platforms/",
     *     summary="Получение записей о всех платформах",
     *     tags={"Platforms"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="Кол-во записей на странице",
     *          @OA\Schema(
     *                type="integer",
     *                example=20
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
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items( ref="#/components/schemas/Platform" )
     *          )
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
     *                      example="The offset field must be a integer"
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  description="Код ответа",
     *                  example=422
     *              )
     *          )
     *      )
     *  )
     *  Получить всей файлы платформы
     *
     * @param  IndexRequest     $request
     * @param  IndexRepository  $indexRepository
     * @return JsonResponse
     */
    public function index(IndexRequest $request, IndexRepository $indexRepository): JsonResponse
    {
        $dto = IndexDTO::fromRequest($request);

        $platforms = $indexRepository->allWith(new Platform(), $dto, 'projects');


        /** Возвращаем результат в виде коллекции */
        return $this->OkResponse($platforms, 'platforms');
    }

    /**
     * @OA\Get(
     *     path="/api/platforms/{platform_id}/",
     *     summary="Получение записи о платформе",
     *     tags={"Platforms"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="platform_id",
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
     *          @OA\JsonContent( ref="#/components/schemas/Platform" )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent( ref="#/components/schemas/404" )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent( ref="#/components/schemas/401" )
     *     )
     *   )
     *  Получение записей платформы
     *
     * @param  int              $id
     * @param  IndexRepository  $indexRepository
     * @return JsonResponse
     */
    public function show(int $id, IndexRepository $indexRepository): JsonResponse
    {
        $platform = $indexRepository->getByObjectWith(new Platform(), $id, 'projects');
        /** Поиск записи по id в Случае ошибки выкинет исключение 404 */
        return $this->OkResponse($platform, 'platform');
    }
}

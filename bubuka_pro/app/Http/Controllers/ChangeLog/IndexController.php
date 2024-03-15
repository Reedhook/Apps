<?php

namespace App\Http\Controllers\ChangeLog;

use App\DTO\IndexDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeLog\IndexRequest;
use App\Models\ChangeLog;
use App\Repositories\IndexRepository;
use Illuminate\Http\JsonResponse;

class IndexController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/changes/",
     *     summary="Получение записей о тугриках",
     *     tags={"ChangeLog"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="Кол-во записей на странице",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *              example=20
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="offset",
     *          in="query",
     *          description="Кол-во пропусков",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *              example=0
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent( ref="#/components/schemas/ChangeLog" )
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
     *     )
     * )
     * Получение записей всех платформы
     *
     * @param  IndexRequest     $request
     * @param  IndexRepository  $indexRepository
     * @return JsonResponse
     */
    public function index(IndexRequest $request, IndexRepository $indexRepository): JsonResponse
    {
        $dto = IndexDTO::fromRequest($request);
        $changelogs = $indexRepository->all(new ChangeLog(), $dto);
        /** Раздельно добавляем условия */

        /** Возвращаем результат в виде коллекции */
        return $this->OkResponse($changelogs, 'changelogs');
    }

    /**
     * @OA\Get(
     *     path="/api/changes/{changes_id}/",
     *     summary="Получение записи о тугрике",
     *     tags={"ChangeLog"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="changes_id",
     *          in="path",
     *          required=true,
     *          description="Id тугрика",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *              example=1
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent( ref="#/components/schemas/ChangeLog" )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent( ref="#/components/schemas/404")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent ( ref="#/components/schemas/401" )
     *     )
     * )
     *  Получение записей платформы
     *
     * @param  int              $id
     * @param  IndexRepository  $indexRepository
     * @return JsonResponse
     */
    public function show(int $id, IndexRepository $indexRepository): JsonResponse
    {
        $changelog = $indexRepository->getByObject(new ChangeLog(), $id );

        return $this->OkResponse($changelog, 'changelog');
    }
}

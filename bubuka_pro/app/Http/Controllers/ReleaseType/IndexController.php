<?php

namespace App\Http\Controllers\ReleaseType;

use App\DTO\IndexDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\File\IndexRequest;
use App\Models\ReleaseType;
use App\Repositories\IndexRepository;
use Illuminate\Http\JsonResponse;

class IndexController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/releases_types/",
     *     summary="Получение всех записей о типах релизов",
     *     tags={"Releases_Types"},
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
     *          @OA\JsonContent( ref="#/components/schemas/Release_Type" )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent( ref="#/components/schemas/500" )
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
     *   )
     *  Получение всех записей о типах релизов
     *
     * @param  IndexRequest     $request
     * @param  IndexRepository  $indexRepository
     * @return JsonResponse
     */
    public function index(IndexRequest $request, IndexRepository $indexRepository): JsonResponse
    {
        $dto = IndexDTO::fromRequest($request);

        $releasesTypes = $indexRepository->all(new ReleaseType(), $dto);

        /** Возвращаем результат в виде коллекции */
        return $this->OkResponse($releasesTypes, 'releases_types');
    }

    /**
     * @OA\Get(
     *        path="/api/releases_types/{release_type_id}/",
     *        summary="Получение записи о типе релиза",
     *        tags={"Releases_Types"},
     *        security={{"bearer_token":{}}},
     *        @OA\Parameter(
     *            name="release_type_id",
     *            in="path",
     *            description="Id типа релиза",
     *            @OA\Schema(
     *                type="integer",
     *                format="int64",
     *                example=3
     *            )
     *        ),
     *        @OA\Response(
     *            response=200,
     *            description="OK",
     *            @OA\JsonContent( ref="#/components/schemas/Release_Type" )
     *        ),
     *        @OA\Response(
     *            response=404,
     *            description="Not Found",
     *            @OA\JsonContent(ref="#/components/schemas/404")
     *        ),
     *        @OA\Response(
     *           response=401,
     *           description="Unauthorized",
     *           @OA\JsonContent( ref="#/components/schemas/401" )
     *        )
     *   )
     *  Получение записи типа релиза
     *
     * @param  int              $id
     * @param  IndexRepository  $indexRepository
     * @return JsonResponse
     */
    public function show(int $id, IndexRepository $indexRepository): JsonResponse
    {
        $releaseType = $indexRepository->getByObject(new ReleaseType(), $id);

        return $this->OkResponse($releaseType, 'release_type');
    }
}

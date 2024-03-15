<?php

namespace App\Http\Controllers\Release;

use App\DTO\IndexDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\File\IndexRequest;
use App\Models\Release;
use App\Repositories\IndexRepository;
use Illuminate\Http\JsonResponse;

class IndexController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/releases/",
     *     summary="Получить данные о всех релизах",
     *     tags={"Releases"},
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
     *          @OA\JsonContent( ref="#/components/schemas/Release" )
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
     *  Получить данные о всех релизах
     *
     * @param  IndexRequest     $request
     * @param  IndexRepository  $indexRepository
     * @return JsonResponse
     */
    public function index(IndexRequest $request, IndexRepository $indexRepository): JsonResponse
    {
        $dto = IndexDTO::fromRequest($request);
        $releases = $indexRepository->all(new Release(), $dto);
        /** Возвращаем результат в виде коллекции */
        return $this->OkResponse($releases, 'releases');
    }

    /**
     * @OA\Get(
     *     path="/api/releases/{release_id}/",
     *     summary="Получение записи",
     *     tags={"Releases"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="release_id",
     *          in="path",
     *          description="Id релиза",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *              example=3
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent( ref="#/components/schemas/Release" )
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
     * )
     *
     *  Получение записи
     *
     * @param  int              $id
     * @param  IndexRepository  $indexRepository
     * @return JsonResponse
     */
    public function show(int $id, IndexRepository $indexRepository): JsonResponse
    {
        $indexRepository->getByObject(new Release(), $id);
        /** Поиск записи по id и подгружаем связанные платформы, в Случае ошибки выкинет исключение 404 */
        return $this->OkResponse(Release::findOrFail($id), 'release');
    }
}

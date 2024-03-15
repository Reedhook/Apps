<?php

namespace App\Http\Controllers\Project;

use App\DTO\IndexDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\IndexRequest;
use App\Models\Project;
use App\Repositories\IndexRepository;
use Illuminate\Http\JsonResponse;

class IndexController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/projects/",
     *     summary="Получение записей о всех проектах",
     *     tags={"Projects"},
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
     *              example=10
     *          ),
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items ( ref="#/components/schemas/Project" )
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
     *                  description="Код ответа", example=422
     *              )
     *          )
     *     )
     *   )
     *  Получение записей всех проектов
     *
     * @param  IndexRequest     $request
     * @param  IndexRepository  $indexRepository
     * @return JsonResponse
     */
    public function index(IndexRequest $request, IndexRepository $indexRepository): JsonResponse
    {
        $dto = IndexDTO::fromRequest($request);

        $projects = $indexRepository->allWith(new Project(), $dto, ['platforms', 'users']);

        /** Возвращаем результат в виде коллекции */
        return $this->OkResponse($projects,'projects');
    }

    /**
     * @OA\Get(
     *     path="/api/projects/{project_id}/",
     *     summary="Получение записей о проекте",
     *     tags={"Projects"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="project_id",
     *          in="path",
     *          required=true,
     *          description="Id проекта",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *              example=3
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent( ref="#/components/schemas/Project" )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(ref="#/components/schemas/404")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent( ref="#/components/schemas/401" )
     *     )
     *  )
     *
     *  Получение записей проекта
     *
     * @param  int              $id
     * @param  IndexRepository  $indexRepository
     * @return JsonResponse
     */
    public function show(int $id, IndexRepository $indexRepository): JsonResponse
    {
        $project = $indexRepository->getByObjectWith(new Project(),$id, ['platforms', 'users'] );
        /** Поиск записи по id и подгружаем связанные платформы, в Случае ошибки выкинет исключение 404 */
        return $this->OkResponse($project, 'project');
    }
}

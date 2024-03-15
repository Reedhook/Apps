<?php

namespace App\Http\Controllers\ReleaseType;

use App\Actions\ReleaseType\UpdateAction;
use App\DTO\ReleaseTypeDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReleaseType\UpdateRequest;
use Illuminate\Http\JsonResponse;

class UpdateController extends Controller
{
    /**
     * @OA\Patch(
     *       path="/api/releases_types/{release_type_id}/",
     *       summary="Изменение записей типов релизов",
     *       tags={"Releases_Types"},
     *       security={{"bearer_token":{}}},
     *       @OA\Parameter(
     *           name="release_type_id",
     *           in="path",
     *           description="Id типа релиза",
     *           required=true,
     *           @OA\Schema(
     *               type="integer",
     *               format="int64",
     *               example=1
     *           )
     *       ),
     *       @OA\RequestBody(
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *                  example="Minor Release",
     *                  description="Название типа релиза"
     *              ),
     *              @OA\Property(
     *                  property="description",
     *                  type="string",
     *                  example="Обновление с небольшими изменениями и улучшениями.",
     *                  description="Описание типа релиза"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *           response=200,
     *           description="OK",
     *           @OA\JsonContent( ref="#/components/schemas/Release_Type" )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  example=false
     *              ),
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example={
     *                          "The name field is required when description is not present.",
     *                          "The name field must be a string.",
     *                          "The name field must not be greater than 255 characters.",
     *                          "The name has already been taken."
     *                      }
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      type="string",
     *                      example={
     *                          "The description field is required when name is not present.",
     *                          "The description field must be a string",
     *                          "The description field must not be greater than 255 characters."
     *                      }
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  example=422
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(ref="#/components/schemas/404")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *          @OA\JsonContent(ref="#/components/schemas/400-Update")
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent( ref="#/components/schemas/500" )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent( ref="#/components/schemas/401" )
     *      )
     *  )
     *  Обновление записей сущности ReleaseType после валидации данных
     *
     * @param  UpdateRequest  $request
     * @param  int            $id
     * @param  UpdateAction   $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, int $id, UpdateAction $action): JsonResponse
    {
        $dto = ReleaseTypeDTO::fromRequest($request);
        $rt = $action->execute($dto, $id);

        return $this->OkResponse($rt, 'release_type');
    }

}

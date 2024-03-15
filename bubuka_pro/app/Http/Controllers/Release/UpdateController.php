<?php

namespace App\Http\Controllers\Release;

use App\Actions\Release\UpdateAction;
use App\DTO\ReleaseDTO;
use App\Exceptions\SameDataException;
use App\Http\Controllers\File\UpdateController as FileController;
use App\Http\Requests\Release\UpdateRequest;
use App\Models\Release;
use App\Services\Release\ReleaseService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class UpdateController extends BaseReleaseController
{
    protected FileController $file;

    public function __construct(ReleaseService $releaseService, FileController $file)
    {
        parent::__construct($releaseService);
        $this->file = $file;
    }

    /**
     * @OA\Post(
     *     path="/api/releases/{release_id}/",
     *     summary="Изменение записей  релиза",
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
     *              example=1
     *          )
     *     ),
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="file",
     *                      description="Файл для загрузки",
     *                      type="file"
     *                  ),
     *                  @OA\Property(
     *                      property="platform_id",
     *                      type="integer",
     *                      format="int64",
     *                      description="Id платформы",
     *                      example=1
     *                  ),
     *                  @OA\Property(
     *                      property="change_id",
     *                      type="integer",
     *                      format="int64",
     *                      description="Id тугрика",
     *                      example=1
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      type="string",
     *                      description="Краткое описание",
     *                      example="Исправлено : ... , ..."
     *                  ),
     *                  @OA\Property(
     *                      property="release_type_id",
     *                      type="integer",
     *                      format="int64",
     *                      description="Id типа релиза",
     *                      example=1
     *                  ),
     *                  @OA\Property(
     *                      property="is_ready",
     *                      type="boolean",
     *                      description="Готов ли релиз к отображению на сайте и к скачивания",
     *                      example=false
     *                  ),
     *                  @OA\Property(
     *                      property="technical_requirement_id",
     *                      type="integer",
     *                      format="int64",
     *                      description="Id технических характеристик",
     *                      example=1
     *                  ),
     *                  @OA\Property(
     *                      property="version",
     *                      type="string",
     *                      description="Версия релиза",
     *                      example="1.0.0"
     *                  )
     *              )
     *          )
     *       ),
     *       @OA\Response(
     *           response=200,
     *           description="OK",
     *           @OA\JsonContent( ref="#/components/schemas/Release" )
     *       ),
     *       @OA\Response(
     *          response=422,
     *          description="Validation Error",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Статус кода",
     *                 example=false
     *              ),
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  description="Сообщение ошибки",
     *                  @OA\Property(
     *                      property="change_id",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля change_id",
     *                      example="The change_id field must be a integer"
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля description",
     *                      example={
     *                         "The description field must be a string",
     *                         "The description field must not be greater than 255 characters.",
     *                      }
     *                  ),
     *                  @OA\Property(
     *                      property="release_type_id",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля description",
     *                      example="The release_type_id field must be a integer"
     *                  ),
     *                  @OA\Property(
     *                      property="is_ready",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля description",
     *                      example="The is_ready field is boolean"
     *                  ),
     *                  @OA\Property(
     *                      property="is_public",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля description",
     *                      example="The is_public field is boolean"
     *                  ),
     *                  @OA\Property(
     *                      property="technical_requirement_id",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля description",
     *                      example="The technical_requirement_id field must be a integer"
     *                  ),
     *                  @OA\Property(
     *                      property="download_url",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля description",
     *                      example="The download_url field must be a string"
     *                  ),
     *                  @OA\Property(
     *                      property="version",
     *                      type="string",
     *                      description="Возможные ошибки валидации поля description",
     *                      example="The version field must be a string"
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  description="Код ответа",
     *                  example=422
     *              )
     *           )
     *       ),
     *       @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent( ref="#/components/schemas/404" )
     *       ),
     *       @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *          @OA\JsonContent( ref="#/components/schemas/400-Update" )
     *       ),
     *       @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent( ref="#/components/schemas/401" )
     *       )
     *  )
     * Обновление записей сущности Release после валидации данных
     *
     * @param  UpdateRequest  $request
     * @param  int            $id
     * @param  UpdateAction   $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, int $id, UpdateAction $action): JsonResponse
    {
        $dto = ReleaseDTO::fromRequest($request);

        $release = $action->execute($dto, $id);

        return $this->OkResponse($release, 'release');
    }

    /**
     * @OA\Patch(
     *      path="/api/releases/confirm_public/{release_id}",
     *      summary="Разрешение/Запрет релиза к публикации",
     *      tags={"Releases"},
     *      security={{"bearer_token":{}}},
     *      @OA\Parameter(
     *             name="release_id",
     *             in="path",
     *             description="Id релиза",
     *             required=true,
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int64",
     *                 example=1
     *             )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  description="Статус ответа",
     *                  example=true
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  description="Сообщение ответа",
     *                  example={
     *                      "Админ подтвердил публикаци",
     *                      "Админ запретил публикацию"
     *                  },
     *              ),
     *              @OA\Property(
     *                  property="code",
     *                  type="integer",
     *                  description="Код ответа",
     *                  example=200
     *              )
     *          )
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
     *      ),
     *      @OA\Response(
     *           response=404,
     *           description="NotFound",
     *           @OA\JsonContent( ref="#/components/schemas/404" )
     *      )
     *  )
     * Подтверждение/Запрет релиза
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function confirm_public(int $id): JsonResponse
    {
        /** Поиск записей по id, в Случае ошибки выкинет исключение 404 */
        $release = Release::findOrFail($id);
        if (!$release['is_public']) {
            return $this->releaseService->confirm_public($release, true);
        } else {
            return $this->releaseService->confirm_public($release, false);
        }
    }

}

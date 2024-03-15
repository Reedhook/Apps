<?php

namespace App\Http\Controllers\TechnicalRequirement;

use App\Actions\TechnicalRequirement\UpdateAction;
use App\DTO\TechnicalRequirementDTO;
use App\Exceptions\SameDataException;
use App\Http\Controllers\Controller;
use App\Http\Requests\TechnicalRequirement\UpdateRequest;
use App\Models\TechnicalRequirement;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UpdateController extends Controller
{
    /**
     * @OA\Patch(
     *     path="/api/techs_reqs/{tech_req_id}/",
     *     summary="Изменение записей о технических характеристик",
     *     tags={"Technicals_Requirements"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="tech_req_id",
     *          in="path",
     *          description="Id тех. хар-и",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *              example=1
     *          )
     *     ),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="os_type",
     *                  type="string",
     *                  description="Операционная система",
     *                  example="Unix"
     *              ),
     *              @OA\Property(
     *                  property="specifications",
     *                  type="string",
     *                  example="ОЗУ 4 ГБ",
     *                  description="Прочие характеристики"
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent( ref="#/components/schemas/Technical_Requirement" )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation Error",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  description="Статус ответа",
     *                  example=false
     *              ),
     *              @OA\Property(
     *                  property="errors",
     *                  description="Ошибки",
     *                  type="object",
     *                  @OA\Property(
     *                      property="os_type",
     *                      type="string",
     *                      example={
     *                          "The os_type field is required when specifications is not present.",
     *                          "The os_type field must be a string.",
     *                          "The os_type field must not be greater than 255 characters."
     *                      }
     *                  ),
     *                  @OA\Property(
     *                      property="specifications",
     *                      type="string",
     *                      example={
     *                          "The specifications field is required when os_type is not present.",
     *                          "The specifications field must be a string",
     *                          "The specifications field must not be greater than 255 characters."
     *                      }
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
     *           response=404,
     *           description="Not Found",
     *           @OA\JsonContent( ref="#/components/schemas/404" )
     *       ),
     *       @OA\Response(
     *           response=400,
     *           description="Bad Request",
     *           @OA\JsonContent( ref="#/components/schemas/400-Update" )
     *       ),
     *        @OA\Response(
     *           response=401,
     *           description="Unauthorized",
     *           @OA\JsonContent( ref="#/components/schemas/401" )
     *        )
     *  )
     *  Обновление записей сущности TechnicalRequirement после валидации данных
     *
     * @param  UpdateRequest  $request
     * @param  int            $id
     * @param  UpdateAction   $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, int $id, UpdateAction $action): JsonResponse
    {
        $dto = TechnicalRequirementDTO::fromRequest($request);
        $tech = $action->execute($dto, $id);

        return $this->OkResponse($tech, 'technical_requirement');
    }

}

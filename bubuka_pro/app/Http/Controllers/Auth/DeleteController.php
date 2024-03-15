<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DeleteController extends Controller
{
    /**
     * @OA\Delete(
     *     path="/api/auth/user/delete",
     *     summary="Удаление пользователя",
     *     tags={"Auth"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  description="Статус ответа",
     *                  example=true
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent( ref="#/components/schemas/401" )
     *     )
     * )
     * Метод для удаления пользователя
     * @return JsonResponse
     */
    public function delete(): JsonResponse
    {
        $user = User::find(auth()->id());
        $user->delete();
        return $this->deleteResponse();
    }
}

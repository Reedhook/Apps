<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ForgotRequest;
use App\Models\User;
use App\Services\Auth\Password\TokenRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * @OA\Post(
     *      path="/api/auth/forgot/",
     *      summary="Запрос на ссылку для сброса пароля",
     *      tags={"Auth","Password"},
     *      @OA\RequestBody(
     *           @OA\JsonContent(
     *               required={"email"},
     *               @OA\Property(
     *                   property="email",
     *                   type="string",
     *                   description="Адрес электронной почты",
     *                   format="email",
     *                   example="user@example.com"
     *               )
     *           )
     *      ),
     *      @OA\Response(
     *           response=200,
     *           description="OK",
     *           @OA\JsonContent(
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  description="Содержимое ответа",
     *                  example="На почту отправлено письмо"
     *              )
     *           )
     *      )
     *  )
     * Метод для отправки ссылки на сброс пароля на почту
     * @param  ForgotRequest  $request
     * @return JsonResponse|Response
     */
    public function sendResetLinkEmail(ForgotRequest $request): JsonResponse|Response
    {
        $user = $this->getUser($request->email);
        $response = app(TokenRepository::class)->create($user);
        return $this->sendResetLinkResponse($request, $response);
    }

    /**
     * Метод для форматирования ответа при отправке
     * @param  Request  $request
     * @param  array    $response
     * @return Response|JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, array $response): Response|JsonResponse
    {
        return $request->wantsJson()
            ? new JsonResponse(['message' => 'На почту отправлено письмо', 'token' => $response['token']], 200)
            : response($response['token']);
    }

    /**
     * Метод для получения информации о пользователе
     * @param  string  $email
     * @return Model
     */
    protected function getUser(string $email): Model
    {
        return User::where('email', $email)->first();
    }
}

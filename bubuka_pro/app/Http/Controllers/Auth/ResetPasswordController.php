<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * @OA\Post(
     *      path="/api/auth/reset/",
     *      summary="Сброс пароля",
     *      security={{"bearer_token":{}}},
     *      tags={"Auth", "Password"},
     *      @OA\RequestBody(
     *           @OA\JsonContent(
     *               required={"email", "password","password_confirmation", "token"},
     *               @OA\Property(
     *                   property="email",
     *                   type="string",
     *                   description="Адрес электронной почты",
     *                   format="email",
     *                   example="user@example.com"
     *               ),
     *               @OA\Property(
     *                   property="password",
     *                   type="string",
     *                   description="Новый пароль",
     *                   example="123456789"
     *               ),
     *               @OA\Property(
     *                   property="password_confirmation",
     *                   type="string",
     *                   description="Новый пароль",
     *                   example="123456789"
     *               ),
     *               @OA\Property(
     *                   property="token",
     *                   type="string",
     *                   description="Reset-Token. В URL ссылке, отправленного письма на почту находится reset-token ",
     *                   example="d85ecc9c496174cf45ac0d2e310883516f0c7bd57aeb0de94d88a47c63833a56"
     *               )
     *           )
     *      ),
     *      @OA\Response(
     *           response=200,
     *           description="OK",
     *           @OA\JsonContent(
     *              @OA\Property (
     *                  property="message",
     *                  type="string",
     *                  description="Содержимое ответа",
     *                  example="Your password has been reset."
     *              )
     *           )
     *      )
     *  )
     * Reset the given user's password.
     *
     * @param  ResetPasswordRequest  $request
     * @return RedirectResponse|JsonResponse
     * @throws ValidationException
     */
    public function reset(ResetPasswordRequest $request): JsonResponse|RedirectResponse
    {

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise, we will parse the error and return the response.
        $response = $this->broker()->reset(
        //Берутся нужные поля
            $this->credentials($request), function ($user, $password) {

            //Сброс пароля
            $this->resetPassword($user, $password);
        }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($request, $response)
            : $this->sendResetFailedResponse($request, $response);
    }
}

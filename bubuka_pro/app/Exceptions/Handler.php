<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;
use function Laravel\Prompts\error;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e): JsonResponse|RedirectResponse|\Illuminate\Http\Response|Response
    {
        /**
         * Проверка на ожидаемый ответ
         */
        if ($request->expectsJson()) {

            return $this->jsonException($e, $request);
        }
        /**
         * Если не json, то обработки идет как задумано библиотеками
         */
        return parent::render($request, $e);
    }


    /**
     * Метод для форматирования ошибки
     * @param  Throwable  $e
     * @param             $request
     * @return Response|RedirectResponse|JsonResponse|\Illuminate\Http\Response
     * @throws Throwable
     */
    public function jsonException(
        Throwable $e,
        $request
    ): \Illuminate\Http\Response|JsonResponse|Response|RedirectResponse {
        if ($e instanceof ModelNotFoundException) {
            /** Не найдена модель. В проекте возникает при обращении по id */
            /** Обработка ошибки 404 */
            $this->logging($e, 404);
            $id = $e->getIds();
            $message = 'Запись не найдена ['.$e->getModel().'] '.$id[0];
            return $this->send_response($e, 404, $message);

        } elseif ($e instanceof UnauthorizedHttpException) {
            /** Обработка ошибок с авторизацией и токеном */
            $this->logging($e, 401);
            return $this->send_response($e, 401);
        } elseif ($e instanceof QueryException) {
            /** Ошибка при записи в базу данных и Ошибка превышение максимального размера загружаемого файла  */
            /** Обработка ошибки 400 */
            $this->logging($e, 400);

            $message = 'Ошибка при работе с бд';
            return $this->send_response($e, 400, $message);

        } elseif ($e instanceof PostTooLargeException) {
            /** Ошибка при записи в базу данных и Ошибка превышение максимального размера загружаемого файла  */
            /** Обработка ошибки 400 */
            $this->logging($e, $e->getStatusCode());
            $message = 'Слишком большой файл';
            return $this->send_response($e, $e->getStatusCode(), $message);

        } elseif ($e instanceof Exception) {
            /** Обработка всех иных ошибок */
            $code = $e->getCode();
            /** Laravel может иногда выдавать ошибки со статусом кода 0. В некоторых местах вручную вызывается исключения */
            $code = ($code == 0) ? 500 : $e->getCode();

            $this->logging($e, $code);
            return $this->send_response($e, $code);
        }
        return parent::render($request, $e);
    }

    /**
     * Метод для отправки ошибки
     * @param  Throwable    $e
     * @param  int          $code
     * @param  string|null  $message
     * @return JsonResponse
     */
    public function send_response(Throwable $e, int $code, string $message = null): JsonResponse
    {
        if (empty($message)) {
            $message = $e->getMessage();
        }
        return response()->json([
            'status' => false,
            'message' => $message,
            'code' => $code
        ],
            $code
        );
    }

    /**
     * Метод для логирования
     * @param $e
     * @param $code
     * @return void
     */
    public function logging($e, $code): void
    {
        Log::error(
            message: '['.get_class($e).'] '.$e->getMessage(),
            context: [
                'code' => $code,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        );
    }

}

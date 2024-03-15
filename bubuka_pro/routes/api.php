<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\Project;
use App\Http\Controllers\Platform;
use App\Http\Controllers\TechnicalRequirement;
use App\Http\Controllers\ReleaseType;
use App\Http\Controllers\ReleaseDownload;
use App\Http\Controllers\Release;
use App\Http\Controllers\File;
use App\Http\Controllers\ChangeLog;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//Route::post('registration', [Auth\RegisterController::class, 'create'])
//    ->name('registration');
Route::group(['middleware' => 'api'], function () {

    // Авторизация
    Route::group(['prefix' => 'auth'], function () {

        // Авторизация
        Route::post('login', [Auth\AuthController::class, 'login'])->name('login');

        // Получение формы для заполнения данных
        Route::get('reset/{token}/', function ($token) {
            return redirect('/users/reset/' . $token);
        })->name('password.reset');

        // Сброс пароля
        Route::post('reset', [Auth\ResetPasswordController::class, 'reset'])->name('password.update');

        //Регистрация
        Route::post('registration', [Auth\RegisterController::class, 'create'])->name('registration');

        // запрос ссылки с токеном на сброс пароля
        Route::post('forgot', [Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.forgot');

        // Обновление токена
        Route::post('refresh', [Auth\AuthController::class, 'refresh'])->name('refresh');
    });

    Route::group(['middleware' => 'jwt.auth'], function () {

        Route::post('testing', [TestController::class, 'test'])->name('test');

        /** Пользователь */
        Route::group(['prefix' => 'auth'], function () {

            // ДеАвторизация
            Route::post('logout', [Auth\AuthController::class, 'logout'])->name('logout');

            // Информация о пользователе
            Route::get('user', [Auth\AuthController::class, 'me'])->name('user');

            // Удаление пользователя
            Route::delete('user/delete', [Auth\DeleteController::class, 'delete'])->name('delete.user');

            Route::get('users', [Auth\AuthController::class, 'users'])->name('index.user')->middleware('admin');
        });

        Route::group(['prefix' => 'platforms'], function () {

            // создание новых записей
            Route::post('/', [Platform\CreateController::class, 'store'])->name('platform.store');

            // изменение записей
            Route::patch('/{id}', [Platform\UpdateController::class, 'update'])->name('platform.update');

            // удаление записей
            Route::delete('/{id}', [Platform\DeleteController::class, 'delete'])->name('platform.delete');

            // получить все данные
            Route::get('/', [Platform\IndexController::class, 'index'])->name('platform.index');

            // получить одну запись
            Route::get('/{id}', [Platform\IndexController::class, 'show'])->name('platform.show');
        });

        /** Проекты */
        Route::group(['prefix' => 'projects'], function () {
            Route::group(['middleware' => 'admin'], function () { // только админ может обращаться по данным маршрутам

                // создание новых записей
                Route::post('/', [Project\CreateController::class, 'store'])->name('project.store');

                Route::match(['patch', 'delete'], '{project_id}/platforms/{platform_id}',
                    [Project\UpdateController::class, 'attachDetach'])->name('project.attachDetach');

                // изменение записей
                Route::patch('{id}', [Project\UpdateController::class, 'update'])->name('project.update');

                // удаление записей
                Route::delete('{id}', [Project\DeleteController::class, 'delete'])->name('project.delete');

                // добавление и удаление пользователя к/из проекта/проекту
                Route::match(['patch', 'delete'], '{project_id}/users/{user_id}',
                    [Project\UpdateController::class, 'addDeleteUser'])->name('project.addDeleteUser');
            });

            // получить все данные
            Route::get('/', [Project\IndexController::class, 'index'])->name('project.index');

            // получить одну запись
            Route::get('{id}', [Project\IndexController::class, 'show'])->name('project.show');
        });

        /** Платформы */


        /** Технические характеристики */
        Route::group(['prefix' => 'techs_reqs'], function () {

            // создание новых записей
            Route::post('/', [TechnicalRequirement\CreateController::class, 'store'])->name('techs_reqs.store');

            // изменение записей
            Route::patch('/{id}', [TechnicalRequirement\UpdateController::class, 'update'])->name('techs_reqs.update');

            // удаление записей
            Route::delete('/{id}',
                [TechnicalRequirement\DeleteController::class, 'delete'])->name('techs_reqs.delete');

            // получить все данные
            Route::get('/', [TechnicalRequirement\IndexController::class, 'index'])->name('techs_reqs.index');

            // получить одну запись
            Route::get('/{id}', [TechnicalRequirement\IndexController::class, 'show'])->name('techs_reqs.show');
        });

        /** Типы Релизов */
        Route::group(['prefix' => 'releases_types'], function () {

            // создание новых записей
            Route::post('/', [ReleaseType\CreateController::class, 'store'])->name('releases_types.store');

            // изменение записей
            Route::patch('/{id}', [ReleaseType\UpdateController::class, 'update'])->name('releases_types.update');

            // удаление записей
            Route::delete('/{id}', [ReleaseType\DeleteController::class, 'delete'])->name('releases_types.delete');

            // получить все данные
            Route::get('/', [ReleaseType\IndexController::class, 'index'])->name('releases_types.index');

            // получить одну запись
            Route::get('/{id}', [ReleaseType\IndexController::class, 'show'])->name('releases_types.show');
        });

        /** Логирование скачиваний релизов */
        Route::group(['prefix' => 'rels_dls'], function () {

            // создание новых записей
            //Route::post('/{id}', [ReleaseDownload\CreateController::class, 'store'])->name('rels_dls.store');

            // изменение записей
            // Route::patch('/{id}', [ReleaseDownload\UpdateController::class, 'update'])->name('rels_dls.update');

            // удаление записей
            Route::delete('/{id}', [ReleaseDownload\DeleteController::class, 'delete'])->name('rels_dls.delete');

            // получить все данные
            Route::get('/', [ReleaseDownload\IndexController::class, 'index'])->name('rels_dls.index');

            // получить одну запись
            Route::get('/{id}', [ReleaseDownload\IndexController::class, 'show'])->name('rels_dls.show');
        });

        /** Релизы */
        Route::group(['prefix' => 'releases'], function () {

            // создание новых записей
            Route::post('/', [Release\CreateController::class, 'store'])->name('releases.store');
            // разрешение/запрет на публикацию
            Route::patch('/confirm_public/{id}',
                [
                    Release\UpdateController::class, 'confirm_public'
                ])->middleware('admin')->name('releases.confirm_public');

            // изменение записей
            Route::post('/{id}', [Release\UpdateController::class, 'update'])->name('releases.update');

            // удаление записей
            Route::delete('/{id}', [Release\DeleteController::class, 'delete'])->name('releases.delete');

            // получить все данные
            Route::get('/', [Release\IndexController::class, 'index'])->name('releases.index');

            // получить одну запись
            Route::get('/{id}', [Release\IndexController::class, 'show'])->name('releases.show');

        });

        /** Файлы */
        Route::group(['prefix' => 'files'], function () {

            //сохранение файла в storage и создание записи в бд
            //Route::post('/', [File\CreateController::class, 'create'])->name('files.save');

            // изменение записей
            //Route::post('/{id}', [File\UpdateController::class, 'update'])->name('files.update');

            // удаление записей
            Route::delete('/{id}', [File\DeleteController::class, 'delete'])->name('files.delete');

            // получить все данные
            Route::get('/', [File\IndexController::class, 'index'])->name('files.index');

            // получить одну запись
            Route::get('/{id}', [File\IndexController::class, 'show'])->name('files.show');
        });

        /** Изменения */
        Route::group(['prefix' => 'changes'], function () {

            // создание тугриков
            Route::post('/', [ChangeLog\CreateController::class, 'store'])->name('changes.store');

            // изменение записей
            Route::patch('/{id}', [ChangeLog\UpdateController::class, 'update'])->name('changes.update');

            // удаление записей
            Route::delete('/{id}', [ChangeLog\DeleteController::class, 'delete'])->name('changes.delete');

            // получить все данные
            Route::get('/', [ChangeLog\IndexController::class, 'index'])->name('changes.index');

            // получить одну запись
            Route::get('/{id}', [ChangeLog\IndexController::class, 'show'])->name('changes.show');
        });
    });
});

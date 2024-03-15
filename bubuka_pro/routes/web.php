<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\File;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/download/{project}/{platform}/{type}/{version}/', [File\IndexController::class, 'download']); // скачивание файла

Route::get('{page}', [\App\Http\Controllers\TestController::class, 'test'])->where('page', '.*');

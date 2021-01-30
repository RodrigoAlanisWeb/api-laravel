<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('signup', [AuthController::class, 'signup']);
});

Route::group(['middleware' => ['auth:api']], function () {
    // Auth
    Route::get('profile', [AuthController::class, 'profile']);
    Route::get('logout', [AuthController::class, 'logout']);
    // Task
    Route::get('tasks', [TaskController::class, 'getAll']);
    Route::get('task/{task}', [TaskController::class, 'getOne']);
    Route::post('task', [TaskController::class, 'create']);
    Route::delete('task/{task}', [TaskController::class, 'delete']);
    Route::put('task/{task}', [TaskController::class, 'edit']);
    Route::get('task/{task}/done', [TaskController::class, 'done']);
});

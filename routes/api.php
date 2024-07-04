<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
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

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('refreshtoken', [AuthController::class, 'refreshToken']);

// Routing with prefix and grouping
Route::prefix('user')->group(function () {
    Route::middleware(['auth:api','auth.scope:superadmin'])->group(function () {
        Route::post('register', [UserController::class, 'register']);
        Route::post('/activate', [UserController::class, 'activate']);
        Route::post('/storeupdate',[UserController::class, 'storeUpdate']);
    });
    Route::middleware(['auth:api','auth.scope:superadmin,user'])->group(function () {
        Route::get('/getall', [UserController::class, 'getAll']);
        Route::get('/getallpending', [UserController::class, 'getAllPending']);
    });
});

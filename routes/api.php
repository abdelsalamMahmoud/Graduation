<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\V1\StudentController;
use App\Http\Controllers\V1\TeacherController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify', [AuthController::class, 'verify']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});
Route::get('/users', [StudentController::class, 'index']);
Route::get('user/{id}', [StudentController::class, 'show']);
Route::post('create_user', [StudentController::class, 'store']);
Route::post('update/{id}', [StudentController::class, 'update']);
Route::post('delete/{id}', [StudentController::class, 'destroy']);
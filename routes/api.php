<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\V1\AdminController;
use App\Http\Controllers\V1\StudentController;
use App\Http\Controllers\V1\TeacherProfileController;
use App\Http\Controllers\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

Route::group(['prefix'=>'v1/admin'],function (){

    //START MANAGE USERS ROUTE
    Route::get('/users', [UserController::class, 'index']);
    Route::get('user/{id}', [UserController::class, 'show']);
    Route::post('create_user', [UserController::class, 'store']);
    Route::post('update/{id}', [UserController::class, 'update']);
    Route::post('delete/{id}', [UserController::class, 'destroy']);
    //END MANAGE USERS ROUTE

});

Route::group(['prefix'=>'v1/student'],function (){
    Route::post('request_plan', [StudentController::class, 'requestPlan']);
    Route::get('get_plans', [StudentController::class, 'get_plans']);
});

Route::group(['prefix'=>'v1/teacher'],function (){
    Route::post('update_info', [TeacherProfileController::class, 'update_info']);
});



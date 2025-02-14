<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\V1\AdminController;
use App\Http\Controllers\V1\CourseController;
use App\Http\Controllers\V1\PlanController;
use App\Http\Controllers\V1\ScheduleController;
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

//START ADMIN ROUTES
Route::group(['prefix'=>'v1/admin','middleware' => ['is_admin']],function (){

    //START MANAGE USERS ROUTE
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::post('/create_user', [UserController::class, 'store']);
    Route::post('/update/{id}', [UserController::class, 'update']);
    Route::post('/delete/{id}', [UserController::class, 'destroy']);
    //END MANAGE USERS ROUTE
});
//END ADMIN ROUTES




//START TEACHER ROUTES
Route::group(['prefix'=>'v1/teacher','middleware' => ['is_teacher']],function (){

    Route::post('/update_info', [TeacherProfileController::class, 'update_info']);

    //SCHEDULES MANAGEMENT
    Route::put('/accept_schedule/{id}', [ScheduleController::class, 'accept']);
    Route::put('/reject_schedule/{id}', [ScheduleController::class, 'reject']);
    Route::delete('/delete_schedule/{id}', [ScheduleController::class, 'destroy']);

    //COURSES MANAGEMENT
    Route::get('/get_course/{id}', [CourseController::class, 'show']);
    Route::post('/create_course', [CourseController::class, 'store']);
    Route::put('/update_course/{id}', [CourseController::class, 'update']);
    Route::put('/publish_course/{id}', [CourseController::class, 'publish_course']);
    Route::delete('/delete_course/{id}', [CourseController::class, 'destroy']);
});
//END TEACHER ROUTES





//START STUDENT ROUTES
Route::group(['prefix'=>'v1/student','middleware' => ['auth:api']],function (){

    //Plans routes
    Route::get('/get_plans', [PlanController::class, 'index']);
    Route::post('/request_plan', [PlanController::class, 'store']);

    //schedule routes
    Route::post('/request_schedule', [ScheduleController::class, 'store']);


});
//END STUDENT ROUTES




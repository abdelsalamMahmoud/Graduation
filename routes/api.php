<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\V1\AdminController;
use App\Http\Controllers\V1\CourseController;
use App\Http\Controllers\V1\ExamController;
use App\Http\Controllers\V1\OptionController;
use App\Http\Controllers\V1\PlanController;
use App\Http\Controllers\V1\QuestionController;
use App\Http\Controllers\V1\RateController;
use App\Http\Controllers\V1\ScheduleController;
use App\Http\Controllers\V1\SessionController;
use App\Http\Controllers\V1\StudentController;
use App\Http\Controllers\V1\TeacherProfileController;
use App\Http\Controllers\V1\UserController;
use App\Http\Controllers\V1\VideoController;
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
    Route::post('delete/multiple/user', [UserController::class, 'deleteMultipleUsers']);
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

    //COURSES VIDEOS MANAGEMENT
    Route::post('/upload_video/{course_id}', [VideoController::class, 'store']);
    Route::delete('/delete_video/{id}', [VideoController::class, 'destroy']);
    Route::put('/update_video/{id}', [VideoController::class, 'update']);
    Route::get('/get_video/{id}', [VideoController::class, 'show']);

    //EXAMS ROUTES
    Route::post('/create_exam', [ExamController::class, 'store']);
    Route::delete('/delete_exam/{id}', [ExamController::class, 'destroy']);
    Route::put('/update_exam/{id}', [ExamController::class, 'update']);
    Route::get('/get_exam/{id}', [ExamController::class, 'show']);
    Route::get('/get_all_exams', [ExamController::class, 'index']);

    //QUESTIONS ROUTES
    Route::post('/create_question/{exam_id}', [QuestionController::class, 'store']);
    Route::delete('/delete_question/{id}', [QuestionController::class, 'destroy']);
    Route::put('/update_question/{id}', [QuestionController::class, 'update']);
    Route::get('/get_question/{id}', [QuestionController::class, 'show']);
    Route::get('/get_all_questions/{exam_id}', [QuestionController::class, 'index']);

    //OPTIONS ROUTES
    Route::post('/create_option/{question_id}', [OptionController::class, 'store']);
    Route::delete('/delete_option/{id}', [OptionController::class, 'destroy']);
    Route::put('/update_option/{id}', [OptionController::class, 'update']);
    Route::get('/get_option/{id}', [OptionController::class, 'show']);
    Route::get('/get_all_options/{question_id}', [OptionController::class, 'index']);

    //RATES ROUTES
    Route::get('/teacher/{teacher_id}/ratings', [RateController::class, 'getTeacherRatings']);
    Route::get('/teacher/{teacher_id}/average-rating', [RateController::class, 'averageRating']);

    //SESSIONS ROUTES
    Route::post('/generate_sessions/{schedule_id}', [SessionController::class, 'generateSessionsForSchedule']);
});
//END TEACHER ROUTES


//START STUDENT ROUTES
Route::group(['prefix'=>'v1/student','middleware' => ['auth:api']],function (){

    //Plans routes
    Route::get('/get_plans', [PlanController::class, 'index']);
    Route::post('/request_plan', [PlanController::class, 'store']);
    Route::post('/update-progress/{studentId}', [PlanController::class, 'updateProgress']);
    Route::get('/{id}/view/student/Progress', [PlanController::class, 'ViewStudentProgress']);

    //schedule routes
    Route::post('/request_schedule', [ScheduleController::class, 'store']);

    //rate routes
    Route::post('/store/rate', [RateController::class, 'StoreRate']);
    Route::post('/rate/{id}', [RateController::class, 'updateRate']);
    Route::delete('/delete/rate/{id}', [RateController::class, 'deleteRate']);

    //STUDENT NOTIFICATIONS
    Route::get('/get_notifications', [StudentController::class, 'get_notifications']);

    //COURSES AND COURSE CONTENT
    Route::get('/get_video/{id}', [VideoController::class, 'show'])->middleware('check.subscription');

    //SUBMIT EXAM
    Route::post('/submit_exam', [ExamController::class, 'submit_exam']);

});

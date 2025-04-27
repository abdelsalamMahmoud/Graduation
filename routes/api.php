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

Route::get('/test',function(){
    return 'APIs work fine';
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
    Route::put('/make_teacher/{id}', [UserController::class, 'make_teacher']);
    Route::put('/assign_link/{user_id}', [UserController::class, 'assign_link']);
    //END MANAGE USERS ROUTE
});
//END ADMIN ROUTES



//START TEACHER ROUTES
Route::group(['prefix'=>'v1/teacher','middleware' => ['is_teacher']],function (){

    Route::post('/update_info', [TeacherProfileController::class, 'update_info']);

    //SCHEDULES MANAGEMENT
    Route::get('/get_schedules_requests', [ScheduleController::class, 'index']);
    Route::put('/accept_schedule/{id}', [ScheduleController::class, 'accept']);
    Route::put('/reject_schedule/{id}', [ScheduleController::class, 'reject']);
    Route::delete('/delete_schedule/{id}', [ScheduleController::class, 'destroy']);
    Route::get('/accepted_schedules', [ScheduleController::class, 'get_accepted_schedules']);

    //COURSES MANAGEMENT
    Route::get('/get_courses', [CourseController::class, 'index']);
    Route::get('/get_course/{id}', [CourseController::class, 'show']);
    Route::post('/create_course', [CourseController::class, 'store']);
    Route::put('/update_course/{id}', [CourseController::class, 'update']);
    Route::put('/publish_course/{id}', [CourseController::class, 'publish_course']);
    Route::delete('/delete_course/{id}', [CourseController::class, 'destroy']);

    //COURSES VIDEOS MANAGEMENT
    Route::get('/get_videos/{course_id}', [VideoController::class, 'index']);
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
    Route::get('/today_sessions', [SessionController::class, 'teacher_today_sessions']);
    Route::put('/cancel_session/{id}', [SessionController::class, 'cancel_session']);
    Route::put('/finish_session/{id}', [SessionController::class, 'finish_session']);
    Route::delete('/delete_session/{id}', [SessionController::class, 'delete']);

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
    Route::get('/schedules_requests_list', [ScheduleController::class, 'schedules_requests_list']);
    Route::delete('/cancel_schedule_request/{id}', [ScheduleController::class, 'cancel_schedule_request']);

    //rate routes
    Route::post('/store/rate', [RateController::class, 'StoreRate']);
    Route::post('/rate/{id}', [RateController::class, 'updateRate']);
    Route::delete('/delete/rate/{id}', [RateController::class, 'deleteRate']);

    //STUDENT NOTIFICATIONS
    Route::get('/get_notifications', [StudentController::class, 'get_notifications']);

    //LATEST TREE NOTIFICATIONS
    Route::get('/latest_notifications', [StudentController::class, 'latestNotifications']);

    // LATEST TWO COURSES
    Route::get('/latest_courses', [StudentController::class, 'latestCourses']);

    // TEACHERS LIST
    Route::get('/teachers_list', [StudentController::class, 'teachers_list']);

    //COURSES ROUTES
    Route::get('/get_courses', [CourseController::class, 'index']);
    Route::get('/show_course/{id}', [CourseController::class, 'show']);

    //COURSES AND COURSE CONTENT
    Route::get('/get_video/{id}', [VideoController::class, 'show'])->middleware('check.subscription');

    //SUBMIT EXAM
    Route::post('/submit_exam', [ExamController::class, 'submit_exam']);

    //RESULTS ROUTES
    Route::get('/get_exams_results', [ExamController::class, 'get_all_results']);
    Route::get('/get_result/{id}', [ExamController::class, 'get_result']);

    //SESSIONS ROUTES
    Route::get('/today_sessions', [SessionController::class, 'student_today_sessions']);
    Route::get('/all_sessions', [SessionController::class, 'student_all_sessions']);


    //STUDENT EXAM
    Route::middleware(['ensure.student.subscription'])->group(function () {
        Route::get('/get_exam/{id}', [ExamController::class, 'show']);
        Route::get('/get_all_exams', [ExamController::class, 'index']);
    });

});



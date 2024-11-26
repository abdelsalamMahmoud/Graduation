<?php

namespace App\Http\Controllers\V1;


use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StorePlanRequest;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;


class StudentController extends Controller
{
    use ApiResopnseTrait;
    public function __construct()
        {
            $this->middleware('auth:api');
        }

        public function requestPlan(StorePlanRequest $request)
        {
            try {
                $taskPerDay = $request->amount / $request->number_of_days ;
                $plan = Plan::create(array_merge(
                    $request->except(['_token']),
                    [
                        'task_per_day'=>$taskPerDay,
                        'user_id'=>Auth::user()->id
                    ]
                ));
                return $this->apiResponse($plan,'your plan is Ready',201);

            } catch (\Exception $e) {
                return $this->apiResponse(null,'Please Try Again',400);
            }
        }

}

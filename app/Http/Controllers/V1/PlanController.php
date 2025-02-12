<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StorePlanRequest;
use App\Models\Plan;

class PlanController extends Controller
{

    public function index()
    {
        try {
            $plans = Plan::where('user_id',auth('api')->user()->id)->paginate(10);
            return $this->apiResponse($plans,'these are your plans',200);

        } catch (\Exception $e) {
            return $this->apiResponse(null,'Please Try Again',400);
        }
    }


    public function store(StorePlanRequest $request)
    {
        try {
            $taskPerDay = $request->amount / $request->number_of_days ;
            $plan = Plan::create(array_merge(
                $request->except(['_token']),
                [
                    'task_per_day'=>$taskPerDay,
                    'user_id'=>auth('api')->user()->id
                ]
            ));
            return $this->apiResponse($plan,'your plan is Ready',201);

        } catch (\Exception $e) {
            return $this->apiResponse(null,'Please Try Again',400);
        }
    }


}

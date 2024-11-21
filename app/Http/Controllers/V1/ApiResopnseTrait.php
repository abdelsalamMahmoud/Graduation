<?php

namespace App\Http\Controllers\V1;

trait ApiResopnseTrait
{
    public function apiResponse($data = null ,$message = null,$status = null){

        $array=[
            'data'=>$data,
            'message'=> $message,
            'status'=> $status,
        ];
        return response()->json($array);

    }

}
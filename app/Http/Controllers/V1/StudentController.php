<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;

class StudentController extends Controller
{
    use ApiResopnseTrait;
    public function __construct()
    {
        $this->middleware('auth:api');
    }
}

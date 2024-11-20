<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    use ApiResopnseTrait;
    public function __construct()
    {
        $this->middleware('is_admin');
    }
}

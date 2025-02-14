<?php

namespace App\Http\Controllers\V1;
use Exception;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Requests\V1\StoreUserRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminController extends Controller
{
    use ApiResopnseTrait;

}
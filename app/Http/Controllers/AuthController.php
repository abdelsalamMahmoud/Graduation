<?php

namespace App\Http\Controllers;
use App\Mail\VerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register','verify']]);
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $validator->validated();
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'email or password is incorrect'], 401);
        }

        $user = auth()->user();

        if (!$user->is_verified) {
            auth()->logout();
            return response()->json(['error' => 'User not verified. Please verify email.'], 403);
        }

        $token = auth('api')->login($user);

        return $this->createNewToken($token);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $verificationCode = rand(100000, 999999);

            $user = User::create(array_merge(
                $validator->validated(),
                [
                    'password' => bcrypt($request->password),
                    'verification_code' => $verificationCode,
                    'verification_expires_at' => now()->addMinutes(10),
                    'is_verified' => false,
                ]
            ));


            Mail::to($user->email)->send(new VerificationMail($user));

            return response()->json([
                'message' => 'User registered. Please verify your email.',
                'user_id' => $user->id,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verify(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'verification_code' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::find($request->user_id);

        if ($user->verification_code !== $request->verification_code) {
            return response()->json(['message' => 'Invalid verification code'], 400);
        }

        if ($user->verification_expires_at < now()) {
            return response()->json(['message' => 'Verification code expired'], 400);
        }

        $user->update(['is_verified' => true, 'verification_code' => null, 'verification_expires_at' => null]);

        return response()->json(['message' => 'email verified successfully.']);
    }



    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    public function userProfile() {
        return response()->json(auth()->user());
    }

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user()
        ]);
    }

}

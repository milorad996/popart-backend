<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!$token = Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'Successfully logged in',
            'user' => Auth::user(),
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }


    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ]);
    }


    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
    public function getMyProfile()
    {

        $activeUser = Auth::user();
        return response()->json($activeUser);
    }

    public function refresh(Request $request)
    {
        $refreshToken = $request->bearerToken();

        if (!$refreshToken) {
            return response()->json(['message' => 'Refresh token is required'], 400);
        }

        try {
            JWTAuth::setToken($refreshToken);
            $newToken = JWTAuth::refresh($refreshToken);


            return response()->json([
                'user' => Auth::user(),
                'authorization' => [
                    'token' => $newToken,
                    'type' => 'bearer',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }
    }
}

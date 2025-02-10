<?php

namespace App\Http\Controllers\Api;

use Illuminate\Auth\AuthenticationException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function userInfo()
    {
        try {
            $user = auth('api')->user();
            return response()->json([
                'status' => true,
                'message' => 'User info fetched successfully',
                'data' => $user
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: Invalid credentials',
            ], 401);
        }
    }
    public function login(Request $request)
    {
        // dd($request->all());
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        $token = $user->createToken($user->name)->plainTextToken;
        $expiresAt = now()->addMinutes(config('sanctum.expiration'))->toDateTimeString();

        return response()->json([
            'status' => true,
            'message' => 'User logged in successfully',
            'data' => $user,
            'token' => $token,
            'expires_at' => $expiresAt,
        ], 200);
    }
}

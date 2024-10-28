<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Log the current authentication state
        \Log::info('User Auth Check:', ['isAuthenticated' => Auth::check()]);

        if (Auth::check()) {
            return response()->json([
                'message' => 'User is already logged in',
                'user' => Auth::user()
            ]);
        }

        if (Auth::attempt($credentials)) {
            // If authentication passes, create a Sanctum token
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;

            // Log the token for debugging
            \Log::info('User Token Created:', ['token' => $token]);

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user
            ]);
        }

        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }



    /**
     * Handle an API logout request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Revoke all tokens for the authenticated user
            // Delete the current access token
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}

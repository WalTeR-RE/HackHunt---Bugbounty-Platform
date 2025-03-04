<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
#use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Helper\JwtHelper;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     * @throws \Exception
     */

    //TO-DO: Add Authentication Logic
    public function register(Request $request)
    {
        $payload = [
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,
        ];

        return response()->json([
            'message' => 'User registered successfully',
            'user' => JwtHelper::generateToken($payload),
        ], 201);
    }

    /**
     * Login user and return JWT token.
     * @throws \Exception
     */
    public function login(Request $request)
    {
        $payload = [
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,
        ];

        return response()->json([
            'message' => 'Login successful',
            'token' => JwtHelper::generateToken($payload),
        ]);
    }

    /**
     * Logout user (revoke token).
     */
    public function logout(Request $request)
    {


        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Refresh JWT token (for JWT-based auth).
     */
    public function refresh(Request $request)
    {
        // Laravel Sanctum doesn't support refresh tokens.
        // You may need to reissue a new token if using Laravel Passport.

        return response()->json([
            'message' => 'Token refreshed successfully',
            'token' => "bla bla",
        ]);
    }

    /**
     * Get authenticated user details.
     */
    public function me(Request $request)
    {
        return response()->json(JwtHelper::decodeToken($request->bearerToken()));
    }
}

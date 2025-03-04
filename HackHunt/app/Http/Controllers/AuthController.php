<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Users;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Helper\JwtHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Enums\UserRole;


class AuthController extends Controller
{
    /**
     * Register a new user.
     * @throws \Exception
     */

    //TO-DO: Add Authentication Logic
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), Users::validationRules());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $payload = [
            'uuid' => (string) Str::uuid(),
            'name' => $request->name,
            'about_me' => $request->about_me,
            'nickname' => $request->nickname,
            'profile_picture' => $request->profile_picture ?? 'default.png',
            'background_picture' => $request->background_picture ?? 'default_background.png',
            'role_id' => 1,
            'rank' => $request->rank ?? 0,
            'country' => $request->country,
            'active' => (bool) ($request->active ?? true),
            'total_points' => 0,
            'accuracy' => 0.00,
            'links' => json_encode($request->links ?? []),
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'birthday' => $request->birthday,
            'password' => Hash::make($request->password),
            'verified' => (bool) ($request->verified ?? false),
            'vulnerabilities_count' => 0,
            'engagement_count' => 0,
        ];

        $user = Users::create($payload);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }


    /**
     * Login user and return JWT token.
     * @throws \Exception
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $user = Users::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'These credentials do not match our records.',
            ],401);
        }


        try{
            $token = JwtHelper::generateToken($user->toArray());
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Error generating JWT token.'. $e->getMessage(),
            ],500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
        ], 200);
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
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $decoded = JwtHelper::decodeToken($request->refresh_token);

            if ($decoded->type !== 'refresh') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid refresh token',
                ], 401);
            }

            $tokens = JwtHelper::generateToken(Users::where('uuid', $decoded->sub)->first()->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'token' => $tokens
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired refresh token',
            ], 401);
        }
    }

    /**
     * Get authenticated user details.
     */
    public function me(Request $request)
    {
        $decoded = JwtHelper::decodeToken($request->bearerToken());
        $user = Users::where('email', $decoded->email)->first();

        return response()->json([$user]);
    }
}

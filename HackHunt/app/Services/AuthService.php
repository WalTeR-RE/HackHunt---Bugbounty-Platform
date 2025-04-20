<?php

namespace App\Services;

use App\Helper\AuthenticateUser;
use App\Models\Users;
use App\Helper\JwtHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class AuthService
{
    public function registerUser(Request $request)
    {
        $validator = Validator::make($request->all(), Users::validationRules());

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors(),
                'status' => 422
            ];
        }

        $payload = [
            'uuid' => (string) Str::uuid(),
            'name' => $request->name,
            'about_me' => $request->about_me,
            'nickname' => $request->nickname,
            'profile_picture' => $request->profile_picture ?? 'default.png',
            'background_picture' => $request->background_picture ?? 'default_background.png',
            'role_id' => 1,
            'rank' => 0,
            'country' => $request->country,
            'active' => (bool) ($request->active ?? true),
            'total_points' => 0,
            'accuracy' => 0.00,
            'links' => json_encode($request->links ?? []),
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'birthday' => $request->birthday,
            'password' => Hash::make($request->password),
            'verified' => false,
            'vulnerabilities_count' => 0,
            'engagement_count' => 0,
        ];

        $user = Users::create($payload);

        return [
            'success' => true,
            'message' => 'User registered successfully',
            'user' => $user,
            'status' => 201
        ];
    }

    public function loginUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors(),
                'status' => 422
            ];
        }

        $user = Users::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return [
                'success' => false,
                'message' => 'These credentials do not match our records.',
                'status' => 401
            ];
        }

        try {
            $token = JwtHelper::generateToken($user->toArray());
            Users::where('uuid', $user['uuid'])->update(['authenticated' => 1]);
            return [
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'status' => 200
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error generating JWT token. ' . $e->getMessage(),
                'status' => 500
            ];
        }
    }

    public function refreshToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors(),
                'status' => 422
            ];
        }

        try {
            $decoded = JwtHelper::decodeToken($request->refresh_token);

            if ($decoded->type !== 'refresh') {
                return [
                    'success' => false,
                    'message' => 'Invalid refresh token',
                    'status' => 401
                ];
            }

            $user = Users::where('uuid', $decoded->sub)->first();
            $tokens = JwtHelper::generateToken($user->toArray());

            return [
                'success' => true,
                'message' => 'Token refreshed successfully',
                'token' => $tokens,
                'status' => 200
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Invalid or expired refresh token',
                'status' => 401
            ];
        }
    }

    /**
     * Logout user (revoke token).
     */
    public function logout(Request $request)
    {
        $user = AuthenticateUser::authenticatedUser($request);
        DB::table('sessions')->where('user_id',$user['uuid'])->delete();
        Users::where('uuid', $user['uuid'])->update(['authenticated' => 0]);
        return response()->json(['message' => 'Logged out successfully']);
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

    public function getAuthenticatedUser(string $token)
    {
        $decoded = JwtHelper::decodeToken($token);
        return Users::where('email', $decoded->email)->first();
    }
}

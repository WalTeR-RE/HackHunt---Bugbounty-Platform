<?php

namespace App\Services;

use App\Helper\AuthenticateUser;
use App\Models\Users;
use App\Helper\JwtHelper;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Services\MailService;

class AuthService
{
    public function registerUser(Request $request)
    {
        

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

    private function generateResetToken($user)
    {
        $token = Str::random(length: 64);
        DB::table('password_reset_tokens')->updateOrInsert([
            'email' => $user->email,
        ],
    [
        'token' => Hash::make($token),
            'active' => true,
            'created_at' => now()
        ]);
        return $token;
    }
    public function sendResetLinkEmail(Request $request)
    {
        $user = Users::where('email', $request->email)->first();
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found',
                'status' => 404
            ];
        }
        $token = $this->generateResetToken($user);
        $data = [
            'name' => $user->name,
            'token' =>  $token
        ];

        Mail::to($user->email)->send(new MailService($data, 'reset'));
        return [
            'success' => true,
            'message' => 'Reset link sent to your email',
            'status' => 200
        ];
    }
    public function resetPassword(Request $request)
    {
        $user = Users::where('email', $request->email)->first();
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found',
                'status' => 404
            ];
        }
        $token = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        $valid = Carbon::parse($token->created_at)->addMinutes(60)->isFuture();
        if (!$token || !Hash::check($request->token, $token->token)||
            !$token->active|| !$valid) {
            DB::table('password_reset_tokens')->where('email', $request->email)->update(['active' => false]);
            return [
                'success' => false,
                'message' => 'Invalid or expired token',
                'status' => 401
            ];
        }

        $user->password = Hash::make($request->password);
        $user->save();
        DB::table('password_reset_tokens')->where('email', $request->email)->update(['active' => false]);
        return [
            'success' => true,
            'message' => 'Password reset successfully',
            'status' => 200
        ];


    }

   
}

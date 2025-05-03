<?php

namespace App\Http\Controllers;

use App\Helper\AuthenticateUser;
use App\Helper\ProgramValidation;
use App\Models\Program;
use App\Models\Users;
use Faker\Extension\Helper;
use Illuminate\Support\Facades\Validator;
use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Services\MailService;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), Users::validationRules());

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors(),
                'status' => 422
            ];
        }
        return $this->authService->registerUser($request);
    }

    public function login(Request $request)
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

        return $this->authService->loginUser($request);
    }

    public function logout(Request $request)
    {
        return $this->authService->logout($request);
    }

    public function refresh(Request $request)
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
        return $this->authService->refreshToken($request);
    }

    public function me(Request $request)
    {
        return $this->authService->me($request);
    }

    // Test Only will remove it later
    public function test(Request $request)
{
    $user = AuthenticateUser::authenticatedUser($request);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 401);
    }

    $data = [
        'name' => $user->name,
        'resetLink' => url('/reset-password/blabla')
    ];

    Mail::to("email@gmail.com")->send(new MailService($data, 'reset'));

    return response()->json([
        'success' => true,
        'message' => 'Email sent successfully'
    ]);
}

    
}

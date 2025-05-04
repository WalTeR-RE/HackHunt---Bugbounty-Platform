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

    public function updateProfile(Request $request)
    {
        $user = AuthenticateUser::authenticatedUser($request);
        if (!$user) {
            return response()->json(['error' => 'No User Found with this data.'], 400);
        }

        $validator = Validator::make($request->all(), Users::validationRules(true,$user->uuid));

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors(),
                'status' => 422
            ];
        }

        return $this->authService->updateProfile($request, $user->uuid);
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
    // 93ed0cda-df8f-4618-ad6e-b087f25d08fe
    
    $boolean = ProgramValidation::userIsOwnerOrAdmin(
        '93ed0cda-df8f-4618-ad6e-b087f25d08fe',
        'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5'
    );
    if ($boolean) {
        return response()->json(['message' => 'User is owner or admin'], 200);
    } else {
        return response()->json(['message' => 'User is not owner or admin'], 403);
    }
}

    
}

<?php

namespace App\Http\Controllers;

use App\Helper\AuthenticateUser;
use App\Helper\ProgramValidation;
use App\Models\Program;
use App\Models\Users;
use Illuminate\Support\Facades\Validator;
use App\Services\AuthService;
use Illuminate\Http\Request;

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

        $program = Program::find("93ed0cda-df8f-4618-ad6e-b087f25d08fe");
        
        $user = AuthenticateUser::authenticatedUser($request);
        $case = ProgramValidation::userOwnsProgram("93ed0cda-df8f-4618-ad6e-b087f25d08fe",$user->uuid);
        
       /* if (!$program) {
            return response()->json(['error' => 'Program not found'], 404);
        }
    
        $owners = $program->owners; 
        return ($owners);*/
    }
    
}

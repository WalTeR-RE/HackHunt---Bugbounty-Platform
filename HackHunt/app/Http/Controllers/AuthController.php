<?php

namespace App\Http\Controllers;

use App\Helper\AuthenticateUser;
use App\Helper\ProgramValidation;
use App\Models\Program;
use App\Models\Report;
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
        return $this->authService->registerUser($request);
    }

    public function login(Request $request)
    {
        return $this->authService->loginUser($request);
    }

    public function logout(Request $request)
    {
        return $this->authService->logout($request);
    }

    public function refresh(Request $request)
    {
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
        ProgramValidation::userOwnsProgram("93ed0cda-df8f-4618-ad6e-b087f25d08fe",$user->uuid);
       /* if (!$program) {
            return response()->json(['error' => 'Program not found'], 404);
        }
    
        $owners = $program->owners; 
        return ($owners);*/
    }
    
}

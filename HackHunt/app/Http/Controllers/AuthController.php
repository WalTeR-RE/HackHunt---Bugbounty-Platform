<?php

namespace App\Http\Controllers;

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

    public function test(Request $request)
    {
        // Assuming you're passing a specific program_id, you need to get the program instance first
        $program = Program::find("93ed0cda-df8f-4618-ad6e-b087f25d08fe"); // Find program by its ID (use UUID or program_id)
    
        if (!$program) {
            return response()->json(['error' => 'Program not found'], 404);
        }
    
        // Retrieve the owners using the relationship
        $owners = $program->owners; // This will return all related users (owners)
    
        // Return the owners (You can customize this as needed)
        return response()->json($owners);
    }
    
}

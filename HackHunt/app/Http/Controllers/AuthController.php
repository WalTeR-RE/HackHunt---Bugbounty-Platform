<?php

namespace App\Http\Controllers;

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
}

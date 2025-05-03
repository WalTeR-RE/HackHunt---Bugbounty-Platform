<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use App\Services\AuthService;

class PasswordResetController extends Controller
{

    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function sendResetLinkEmail(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'status' => 422
            ]);
        }

        
        $data = $this->authService->sendResetLinkEmail($request);

        return response()->json($data);

    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'status' => 422
            ]);
        }

        $data = $this->authService->resetPassword($request);

        return response()->json($data);
    }
}

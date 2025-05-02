<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdminService;
use Illuminate\Support\Facades\Validator;
use App\Models\Users;
use App\Helper\AuthenticateUser;
use App\Helper\ProgramValidation;
use App\Models\Program;

class AdminController extends Controller
{
    protected $adminService;
    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function createSuperUser(Request $request)
    {

        $validator = Validator::make($request->all(), Users::validationRules());
        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors(),
                'status' => 422
            ];
        }

        
        $admin = $this->adminService->createSuperUser($request);

        return response()->json([
            'success' => true,
            'message' => 'Admin created successfully',
            'admin' => $admin
        ]);
    }

}

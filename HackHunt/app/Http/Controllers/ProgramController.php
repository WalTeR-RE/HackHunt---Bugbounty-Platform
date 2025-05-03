<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProgramService;
use Illuminate\Support\Facades\Validator;
class ProgramController extends Controller
{
    //
    protected $programService;

    public function __construct(ProgramService $programService)
    {
        $this->programService = $programService;
    }

    public function index(Request $request)
    {
        try {
            $programs = $this->programService->getAllPrograms($request);
            return response()->json($programs);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch programs',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getProgramData(Request $request, string $uuid)
    {
        try {
            $program = $this->programService->getProgramById($uuid,$request);
            return response()->json($program);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch program data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $uuid)
    {

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|unique:programs,name,' . $uuid,
                'bounty_range' => 'nullable|string',
                'fast_description' => 'nullable|string',
                'rewards' => 'required|array|size:4',
                'rewards.*.type' => 'required|string|in:cash,points',
                'rewards.*.amount' => ['required', 'regex:/^\d{1,6}-\d{1,6}$/'],
                'target_description' => 'nullable|string',
                'scope' => 'nullable|array',
                'description_rules' => 'nullable|string',
                'status' => 'required|string|in:Active,Stopped',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $program = $this->programService->updateProgram($request, $uuid);


            return response()->json([
                'success' => true,
                'message' => 'Program updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update program',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy(Request $request, string $uuid)
    {
    try {
        $this->programService->deleteProgram($uuid, $request);

        return response()->json([
            'success' => true,
            'message' => 'Program deleted successfully'
        ]);

    } catch (\Exception $e) {
        
        $statusCode = is_numeric($e->getCode()) ? (int)$e->getCode() : 500;

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], $statusCode);
    }
}

    public function store(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:programs,name|max:255',
                'logo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'is_private'=> 'required|boolean',
                'fast_description' => 'required|string',
                'rewards' => 'required|array|size:4',
                'rewards.*.type' => 'required|string|in:cash,points',
                'rewards.*.amount' => ['required', 'regex:/^\d{1,6}-\d{1,6}$/'],
                'target_description' => 'required|string',
                'scope' => 'required|array',
                'description_rules' => 'required|string',
                'status' => 'required|string|in:Active,Stopped',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $program = $this->programService->createProgram($request);

            return response()->json($program);
        }
        catch (\Exception $e) {
        
            $statusCode = is_numeric($e->getCode()) ? (int)$e->getCode() : 500;
    
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function inviteResearcher(Request $request, string $uuid)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $response = $this->programService->inviteResearcher($request, $uuid);

            return response()->json([
                $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send invitation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function removeResearcher(Request $request, string $uuid)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $this->programService->removeResearcher($request, $uuid);

            return response()->json([
                'success' => true,
                'message' => 'Researcher removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove researcher',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function acceptrejectInvite(Request $request, string $uuid)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:Accepted,Rejected'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $this->programService->acceptrejectInvite($request, $uuid);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept invitation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function leaveProgram(Request $request, string $uuid)
    {
        try {
            $data = $this->programService->leaveProgram($request, $uuid);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to leave program',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getInvites(Request $request)
    {
        try {
            $invites = $this->programService->getInvites($request);

            return response()->json($invites);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch invites',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

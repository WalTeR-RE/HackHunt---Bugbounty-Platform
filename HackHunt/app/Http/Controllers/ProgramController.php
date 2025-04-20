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

    public function update(Request $request, $uuid)
    {

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'bounty_range' => 'nullable|string',
                'number_of_reports' => 'nullable|integer',
                'avg_bounty' => 'nullable|numeric',
                'vulnerabilities_rewarded' => 'integer',
                'fast_description' => 'nullable|string',
                'rewards' => 'nullable|string',
                'target_description' => 'nullable|string',
                'scope' => 'nullable|string',
                'description_rules' => 'nullable|string',
                'status' => 'string',
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

}

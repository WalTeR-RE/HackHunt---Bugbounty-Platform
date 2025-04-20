<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Program;
use App\Services\ProgramService;
class EditProgramController extends Controller
{
    protected $programService;

    public function __construct(ProgramService $programService)
    {
        $this->programService = $programService;
    }

    public function update(Request $request, $uuid)
    {
        try {
            $program = $this->programService->updateProgram($request, $uuid);

            return response()->json([
                'success' => true,
                'message' => 'Program updated successfully',
                'data'=>$program
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

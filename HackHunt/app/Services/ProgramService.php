<?php

namespace App\Services;
use Illuminate\Http\Request;
use App\Models\Users;
use App\Helper\ProgramValidation;
use App\Models\Program;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\AuthenticateUser;

class ProgramService{
    public function updateProgram(Request $request, $uuid)
    {
        $user = AuthenticateUser::authenticatedUser($request);
        $allowed = ProgramValidation::userOwnsProgram($uuid, $user->uuid);
        if (!$allowed && $user->role_id !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 401);
        }

        $fields = [
            'name',
            'bounty_range',
            'number_of_reports',
            'avg_bounty',
            'vulnerabilities_rewarded',
            'fast_description',
            'rewards',
            'target_description',
            'scope',
            'description_rules',
            'status',
        ];

        $data = $request->only($fields);
        $data = array_filter($data, fn($value) => !is_null($value));

        $updated = Program::where('program_id', $uuid)->update($data);

        return response()->json([
            'success' => (bool) $updated,
            'message' => $updated ? 'Program updated successfully' : 'No changes made',
        ]);
    }

}

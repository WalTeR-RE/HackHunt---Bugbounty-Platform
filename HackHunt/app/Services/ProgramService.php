<?php

namespace App\Services;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use App\Models\Users;
use App\Helper\ProgramValidation;
use App\Models\Program;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\AuthenticateUser;
use Illuminate\Support\Str;
use function Symfony\Component\Clock\now;

class ProgramService{

    protected function generateBountyrange($rewards){
        $min = $rewards[0]['amount'];
        $max = $rewards[3]['amount'];
        $arr_min = explode('-',$min);
        $arr_max = explode('-',$max);
        $range="$arr_min[0]\$-$arr_max[1]\$";
        
        return $range;
    }
    public function createProgram(Request $request){
        $payload = [
            'program_id'=>(string)Str::uuid(),
            'name' => $request->name,
            'bounty_range'=> $this->generateBountyrange($request->rewards),
            'is_private'=>(bool)$request->is_private,
            'number_of_reports'=> 0,
            'avg_bounty'=>0,
            'validation_time'=>0,
            'vulnerabilities_rewarded'=>0,
            'started_at'=>Carbon::now(),
            'fast_description'=>(string)$request->fast_description,
            'rewards'=>$request->rewards,
            'target_description'=>(string)$request->target_description,
            'scope'=>$request->scope,
            'description_rules'=>(string)$request->description_rules,
            'status'=>(string)$request->status
        ];

        $program = Program::create($payload);

        return [
            'success' => true,
            'message' => 'Program created successfully',
            'Program' => $program,
            'status' => 201
        ];



    }
    public function updateProgram(Request $request, $uuid)
    {
        $user = AuthenticateUser::authenticatedUser($request);
        $allowed = ProgramValidation::userOwnsProgram($uuid, $user->uuid);
        if (!$allowed && $user->role_id !== 3&& ($allowed&& $user->role_id !== 2)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 401);
        }

        $fields = [
            'name',
            'bounty_range',
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

    public function deleteProgram($uuid, Request $request): bool
    {
        $user = AuthenticateUser::authenticatedUser($request);
        $isAdmin = ($user->role_id === 3);
        $isOwner = ProgramValidation::userOwnsProgram($uuid, $user->uuid);

        if (!$isAdmin && !$isOwner && ($isOwner&& $user->role_id !== 2)) {
            throw new \Exception('Forbidden: Requires admin or owner privileges', 403);
        }

        $program = Program::where('program_id', $uuid)->firstOrFail();
        return $program->delete();
    }
}

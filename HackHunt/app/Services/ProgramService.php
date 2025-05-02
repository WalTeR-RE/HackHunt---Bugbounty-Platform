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
use App\Models\ProgramInvite;
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
    public function getAllPrograms(Request $request)
    {
        $user = AuthenticateUser::authenticatedUser($request);
        $isAdmin = ($user->role_id === 3);

        if ($isAdmin) {
            return Program::all();
        }
        $user_private_programs = DB::table('programs')
            ->join('program_user', 'programs.program_id', '=', 'program_user.program_id')
            ->where('program_user.user_id', $user->uuid)
            ->where('programs.is_private', true)
            ->select('programs.*')
            ->get();

        $public_programs = DB::table('programs')
            ->where('is_private', false)
            ->select('programs.*')
            ->get();
        $programs = $public_programs->merge($user_private_programs);
        $programs = $programs->map(function ($program) {
            $program->rewards = json_decode($program->rewards);
            $program->scope = json_decode($program->scope);
            return $program;
        });

        return $programs;
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
        if ($program) {
            $user = AuthenticateUser::authenticatedUser($request);
            $program->insertProgramUser($user->uuid);
        }
        if (!$program) {
            return [
                'success' => false,
                'message' => 'Failed to create program',
                'status' => 500
            ];
        }

        return [
            'success' => true,
            'message' => 'Program created successfully',
            'Program' => $program,
            'status' => 201
        ];
    }

    public function getProgramById($uuid, Request $request)
    {
        $user = AuthenticateUser::authenticatedUser($request);
    
        if (!$user) {
            throw new \Exception('Unauthorized: No user found.', 401);
        }
    
        $isAdmin = ($user->role_id === 3);
        $program = Program::where('program_id', $uuid)->first();
        if (!$isAdmin|| $program->is_private === true) {
            $isOwner = ProgramValidation::userOwnsProgram($uuid, $user->uuid);
            
            $hasAccessToPrivate = DB::table('program_user')
                ->join('programs', 'program_user.program_id', '=', 'programs.program_id')
                ->where('programs.program_id', $uuid)
                ->where('programs.is_private', true)
                ->where('program_user.user_id', $user->uuid)
                ->exists();
    
            if (!$isOwner && !$hasAccessToPrivate) {
                throw new \Exception('Forbidden: Requires admin or owner privileges', 403);
            }
        }
    
        if (!$program) {
            throw new \Exception('Program not found', 404);
        }
    
        return $program;
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

    public function inviteResearcher($request, $uuid)
    {
        $user = AuthenticateUser::authenticatedUser($request);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'No User Found with this data.',
                'status' => 400
            ];
        }
        $program = Program::where('program_id', $uuid)->first();
        if (!$program) {
            return [
                'success' => false,
                'message' => 'Program not found',
                'status' => 404
            ];
        }

        $allowed = ProgramValidation::userOwnsProgram($uuid, $user->uuid);
        if (!$allowed && $user->role_id !== 3) {
            return [
                'success' => false,
                'message' => 'Forbidden',
                'status' => 401
            ];
        }
        $researcher = Users::where('email', $request->email)->first();
        if (!$researcher) {
            return [
                'success' => false,
                'message' => 'Researcher not found',
                'status' => 404
            ];
        }

        ProgramInvite::updateOrCreate(
            [
                'program_id' => $uuid,
                'user_id' => $researcher->uuid,
            ],
            [
                'status' => 'Pending', 
                'invited_by' => $user->uuid,
                'expire_at' => Carbon::now()->addDays(7),
            ]
        );
        

        
        return [
            'success' => true,
            'message' => 'Researcher invited successfully',
            'status' => 200
        ];
    }
}

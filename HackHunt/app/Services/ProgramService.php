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
use App\Services\MailService;
use Illuminate\Support\Facades\Mail;

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
        $logo = null;
    if ($request->hasFile('logo')) {
        $file = $request->file('logo');
        $originalFileName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = $request->name.'.' . $extension;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file->getPathname());
        finfo_close($finfo);

        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];

        if (!in_array($mime, $allowedMimes)) {
            return response()->json(['error' => 'Invalid file type for logo.', 'code' => 422], 422);
        }

        $logo = $file->storeAs('logo', $fileName, 'public');
    }
        $payload = [
            'program_id'=>(string)Str::uuid(),
            'name' => $request->name,
            'logo' => $logo,
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
        if (!$isAdmin&& $program->is_private === true) {
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
        $scope = array_map(function($url) {
            return [
                'target' => $url,
                'type' => 'Web',       
                'in_scope' => true    
            ];
        }, $program->scope);
        $program->scope = $scope;
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
        
        $program = Program::where('program_id', $uuid)
        ->where('is_private',true)
        ->first();
        if (!$program) {
            return [
                'success' => false,
                'message' => 'Program not found or not private',
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
        
        if($allowed && $request->email === $user->email){
            return [
                'success' => false,
                'message' => 'You cannot invite yourself',
                'status' => 400
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

        if($allowed && $researcher->role_id === 2){
            return [
                'success' => false,
                'message' => 'You cannot invite other Owners',
                'status' => 400
            ];
        }

        $currentUsers = DB::table('program_user')
            ->where('program_id', $uuid)
            ->where('user_id', $researcher->uuid)
            ->first();
        if ($currentUsers) {
            return [
                'success' => false,
                'message' => 'Researcher already in program',
                'status' => 400
            ];
        }

        $invited = ProgramInvite::where('program_id', $uuid)
            ->where('user_id', $researcher->uuid)
            ->first();
        

        if ($invited) {
            if ($invited->status === 'Pending' && $invited->expire_at > now()) {
                return [
                    'success' => false,
                    'message' => 'Invitation already sent',
                    'status' => 400
                ];
            } elseif ($invited->expire_at < now()) {
                ProgramInvite::where('program_id', $uuid)
                ->where('user_id', $researcher->uuid)
                ->update([
                    'status' => 'Pending',
                    'expire_at' => Carbon::now()->addDays(7)
                ]);
            
            }
        } else {
            ProgramInvite::create([
                'program_id' => $uuid,
                'user_id' => $researcher->uuid,
                'status' => 'Pending',
                'invited_by' => $user->uuid,
                'expire_at' => Carbon::now()->addDays(7)
            ]);
        }
        
        // TODO: Send email to researcher
        
        $data = [
            'name' => $researcher->name,
            'program_name' => $program->name,
            'link' => url('https://google.com'),
        ];
    
        Mail::to($researcher->email)->send(new MailService($data, 'invite'));


        return [
            'success' => true,
            'message' => 'Researcher invited successfully',
            'status' => 200
        ];
    }
    public function removeResearcher($request, $uuid)
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

        ProgramInvite::where('program_id', $uuid)
            ->where('user_id', $researcher->uuid)
            ->delete();

        $stat = DB::table('program_user')
            ->where('program_id', $uuid)
            ->where('user_id', $researcher->uuid)
            ->delete();

        if ($stat) {
            return [
                'success' => true,
                'message' => 'Researcher removed successfully',
                'status' => 200
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to remove researcher',
                'status' => 500
            ];
        }
        
    }

    public function acceptrejectInvite($request, $uuid)
    {
        $user = AuthenticateUser::authenticatedUser($request);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'No User Found with this data.',
                'status' => 400
            ];
        }

        if($user->role_id !== 1){
            return [
                'success' => false,
                'message' => 'You cannot accept or reject an invitation',
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
        $invite = ProgramInvite::where('program_id', $uuid)
            ->where('user_id', $user->uuid)
            ->where('expire_at', '>', now())
            ->where('status', 'Pending')
            ->first();
            
        if (!$invite) {
            ProgramInvite::where('program_id', $uuid)
                ->where('user_id', $user->uuid)
                ->where('expire_at', '<', now())
                ->update(['status' => 'Expired']);
            return [
                'success' => false,
                'message' => 'Invitation not found',
                'status' => 404
            ];
        }
        
        $stat = ProgramInvite::where('program_id', $uuid)
            ->where('user_id', $user->uuid)
            ->where('expire_at', '>', now())
            ->where('status', 'Pending')
            ->update(['status' => $request->status]);

        if ($request->status === "Accepted") {
            DB::table('program_user')->insert([
                'program_id' => $uuid,
                'user_id' => $user->uuid,
            ]);
        }

        return [
            'success' => true,
            'message' => "Invitation {$request->status}ed successfully",
            'status' => 200
        ];
    }

    public function leaveProgram($request, $uuid)
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
        if ($allowed && $user->role_id === 2) {
            return [
                'success' => false,
                'message' => 'You cannot leave a program you own',
                'status' => 400
            ];
        }

        if(!$allowed){
            return [
                'success' => false,
                'message' => 'You are not a member of this program',
                'status' => 401
            ];
        }

        DB::table('program_user')
            ->where('program_id', $uuid)
            ->where('user_id', $user->uuid)
            ->delete();

        return [
            'success' => true,
            'message' => 'Left program successfully',
            'status' => 200
        ];
    }
    
    public function getInvites($request)
    {
        $user = AuthenticateUser::authenticatedUser($request);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'No User Found with this data.',
                'status' => 400
            ];
        }
        
        $invites = ProgramInvite::where('user_id', $user->uuid)
            ->where('expire_at', '>', now())
            ->where('status', 'Pending')
            ->get();

        return [
            'success' => true,
            'invites' => $invites,
            'status' => 200
        ];
    }
}

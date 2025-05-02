<?php

namespace App\Services;

use App\Models\User;
use App\Models\Users;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminService
{
    public function __construct()
    {
        
    }

    public function createSuperUser($request)
    {
        $user = new Users();
        $user->uuid = (string) Str::uuid();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role_id = $request->role_id;
        $user->nickname = $request->nickname;
        $user->about_me = $request->about_me;
        $user->profile_picture = $request->profile_picture;
        $user->background_picture = $request->background_picture;
        $user->rank = 0;
        $user->country = $request->country;
        $user->total_points = 0;
        $user->accuracy = 0;
        $user->links = json_encode($request->links);
        $user->phone_number = $request->phone_number;
        $user->birthday = $request->birthday;
        $user->email_verified_at = now();
        $user->remember_token = null;
        $user->vulnerabilities_count = 0;
        $user->engagement_count = 0;
        $user->active = true;
        $user->verified = true;
        $user->save();
        return $user;
    }

    
}
<?php

namespace App\Helper;

use App\Helper\JwtHelper;
use App\Models\Users;
use Illuminate\Http\Request;

class AuthenticateUser
{
    public static function authenticatedUser(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return null;
        }

        try {
            $decodedToken = JwtHelper::decodeToken($token);
            return Users::find($decodedToken->sub);
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getUserByNickname($nickname)
    {
        return Users::where('nickname', $nickname)->first();
    }

    public static function updateUsersRanks(){
        $users = Users::orderBy('total_points', 'desc')->get();
        for($rank = 1; $rank <= count($users); $rank++){
            $user = Users::find($users[$rank-1]->uuid);
            if($user){
                $user->rank = $rank;
                $user->save();
            }
        }
        
    }
}

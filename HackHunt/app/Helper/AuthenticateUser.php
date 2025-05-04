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
}

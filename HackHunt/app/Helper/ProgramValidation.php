<?php

namespace App\Helper;

use App\Models\Program;
use App\Models\Users;

class ProgramValidation
{
    /**
     * Check if a given user is an owner of the specified program.
     *
     * @param string $programUuid
     * @param string $userUuid
     * @return bool
     */
    public static function userOwnsProgram(string $programUuid, string $userUuid): bool
    {
       $program = Program::find($programUuid);
       $Owners = $program->owners;

       foreach($Owners as $user){
        if($user->uuid === $userUuid){
            return true;
        }
       }
       return false;
    }
}
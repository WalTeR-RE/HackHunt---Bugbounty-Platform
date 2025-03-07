<?php

namespace App\Http\Middleware;

use App\Models\Users;
use App\Helper\AuthenticateUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permissions = null): Response
    {
        $permissions = explode('|',$permissions);
        $user = AuthenticateUser::authenticatedUser($request);

        if(!isset($user) || !$this->hasRequiredPermission($user,$permissions)){
            return response()->json([
                "message" => "No Permission"
            ], 403);
        }

        return $next($request);
    }

    private function hasRequiredPermission($user, $permissions){

        foreach ($permissions as $required_permission) {
            if($user->hasPermission($required_permission)){
                return true;
            }
        }
        return false;
    }
}

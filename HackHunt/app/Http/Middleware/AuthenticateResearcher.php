<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use App\Models\Users;
use App\Enums\UserRole;

class AuthenticateResearcher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json(['message' => 'Not authorized'], 401);
            }



            $publicKey = env('JWT_PUBLIC_KEY');
            if (!$publicKey) {
                return response()->json(['message' => 'JWT public key not configured'], 500);
            }


            $key = "-----BEGIN PUBLIC KEY-----\n$publicKey\n-----END PUBLIC KEY-----";



            $decoded_token = JWT::decode($token, new Key($key, 'RS256'));

            #return response()->json(['message' => $decoded_token], 200);

            if (!isset($decoded_token->role) || intval(UserRole::from($decoded_token->role)->label()) < intval(UserRole::RESEARCHER->label())) {
                return response()->json(['message' => 'Forbidden!'], 403);
            }
            
            $out = Users::where('uuid', $decoded_token->sub)
            ->where('authenticated', 1)
            ->first();

            if(!$out){
                return response()->json(['message' => 'Forbidden!'], 403);
            }
            return $next($request);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Invalid token',
                'error' => $exception->getMessage()
            ], 401);
        }
    }
}

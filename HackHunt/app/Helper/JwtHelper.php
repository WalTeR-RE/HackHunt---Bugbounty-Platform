<?php

namespace App\Helper;
use App\Enums\UserRole;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\DB;
class JwtHelper
{
    public static function generateToken(array $user): array
    {
        $privateKey = env("JWT_PRIVATE_KEY");

        if (!$privateKey) {
            throw new \Exception("Private key not set");
        }

        $key = "-----BEGIN PRIVATE KEY-----\n$privateKey\n-----END PRIVATE KEY-----";

        $currentTime = time();

        $payload = [
            'sub' => $user['uuid'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => UserRole::fromNumber($user['role_id']),
            'iat' => $currentTime,
            'exp' => $currentTime + 3600,
        ];

        $accessToken = JWT::encode($payload, $key, 'RS256');

        $refreshTokenPayload = [
            'sub' => $user['uuid'],
            'type' => 'refresh',
            'iat' => $currentTime,
            'exp' => $currentTime + (30 * 24 * 60 * 60),
        ];

        $refreshToken = JWT::encode($refreshTokenPayload, $key, 'RS256');

        DB::table('sessions')->updateOrInsert(
            ['user_id' => $user['uuid']],
            [
                'id' => $user['uuid'],
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'payload' => '',
                'remember_token' => $refreshToken,
                'last_activity' => $currentTime
            ]
        );

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => 3600,
        ];
    }

    public static function decodeToken(string $jwt)
    {
        $publicKey = env('JWT_PUBLIC_KEY');
        if (!$publicKey) {
            return response()->json(['message' => 'JWT public key not configured'], 500);
        }


        $key = "-----BEGIN PUBLIC KEY-----\n$publicKey\n-----END PUBLIC KEY-----";


        return JWT::decode($jwt, new Key($key, 'RS256'));

    }
}

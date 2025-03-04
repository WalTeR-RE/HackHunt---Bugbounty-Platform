<?php

namespace App\Helper;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Config;
class JwtHelper
{
    public static function generateToken(array $payload): string
    {
        $privateKey = env("JWT_PRIVATE_KEY");

        if(!$privateKey) {
            throw new \Exception("Private key not set");
        }

        $key = "-----BEGIN PRIVATE KEY-----\n$privateKey\n-----END PRIVATE KEY-----";

        $payload['iat'] = time();
        $payload['exp'] = time() + 3600;

        return JWT::encode($payload, $key,'RS256');
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

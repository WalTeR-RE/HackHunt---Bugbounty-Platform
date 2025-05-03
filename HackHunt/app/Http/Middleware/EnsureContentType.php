<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureContentType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $method = $request->getMethod();
        $contentType = $request->header('Content-Type');

        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'])) {
            return $next($request);
        }

        if (str_starts_with($contentType, 'application/json')) {
            return $next($request);
        }

        if (str_starts_with($contentType, 'multipart/form-data')) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Content-Type must be application/json or multipart/form-data',
        ], 415);
    }
}

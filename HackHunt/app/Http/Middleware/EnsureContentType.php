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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $contentType = $request->header('Content-Type');

        if ($contentType !== 'application/json' && $request->getMethod() !== 'GET') {
            return response()->json([
                'success' => false,
                'message' => 'Content-Type must be application/json'
            ], 415);
        }

        return $next($request);
    }
}

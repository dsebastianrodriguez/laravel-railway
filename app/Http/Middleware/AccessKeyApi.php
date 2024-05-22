<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AccessKeyApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiUser = $request->headers->get('x-api-user');
        $apiKey = $request->headers->get('x-api-key');

        $validUser = env('X_API_USER');
        $validPasswordHash = env('X_API_KEY');

        if ($apiUser !== $validUser || !Hash::check($apiKey, $validPasswordHash)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // if ($apiKey != env('X_API_KEY')){
        //     return response()->json(['message' => 'Unauthorized']);
        // }
        return $next($request);
    }
}

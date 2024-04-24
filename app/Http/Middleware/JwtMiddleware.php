<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try{
            JWTAuth::parseToken()->authenticate();

        }catch(Exception $e){
            if($e instanceof TokenInvalidException){
                $data = [
                    'message' => 'invalid token',
                    'status' => 401
                ];
                return response()->json($data,401);
            }

            if($e instanceof TokenExpiredException){
                $data = [
                    'message' => 'token expired',
                    'status' => 401
                ];
                return response()->json($data,401);
            }
            
            $data = [
                'message' => 'token not found',
                'status' => 401
            ];
            return response()->json($data,401);


        }
        return $next($request);
    }
}

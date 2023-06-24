<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;


class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not found.'], 500);
            }
        } catch (\Exception $e) {
            if ($e instanceof TokenExpiredException) {
                //$token=authf::refresh();
                //$ttl=authf::factory()->getTTL() * 60;
                //$tokenToSend=AuthController::fullToken($token);
                return response()->json([
                    'status' => 'expired token' //, 'data'=>$token
                ], 401);
            } else if ($e instanceof TokenInvalidException) {
                return response()->json(['status' => 'invalid token'], 401);
            } else {
                return response()->json(['status' => 'token not found'], 401);
            }
        }
        return $next($request);
    }
}

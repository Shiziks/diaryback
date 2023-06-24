<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;


class VerifyEmail
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
        //$user=User::all();
        $mail = $request->email;
        $user = User::where('email', $mail)->get()->first();
        if ($user) {
            if ($user->hasVerifiedEmail()) {
                return $next($request);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email must be verified.'
                ], 403);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Email or password are incorrect.'
            ], 403);
        }
    }
}

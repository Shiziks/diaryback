<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;


class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     * @param \Closure(\Illuminate\Http\Request ): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse $next

     */

    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => "Something went wrong"
            ], 400);
        }
    }
}

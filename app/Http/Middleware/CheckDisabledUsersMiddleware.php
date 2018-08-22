<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;

class CheckDisabledUsersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        if (!$user) return;
        if ($user->disabled == 1) {
          JWTAuth::invalidate($request->token);
          return response()->json([
            'success' => false,
            'code' => 401,
            'message' => 'account disabled',
          ], 401);
        }

        return $next($request);
    }
}

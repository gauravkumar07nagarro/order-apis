<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

/**
 * Class OrderApiAuthentication
 *
 * It will be used to authorized the api access, for the time being it is not functional
 */

class OrderApiAuthentication
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
        $token = $request->header('authorization');

        if ( $token == config('auth.api_token')) {
            return $next($request);
        }

        return response()->json([], Response::HTTP_UNAUTHORIZED);
    }
}

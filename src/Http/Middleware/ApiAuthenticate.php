<?php

namespace Sudo\ApiUser\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ApiAuthenticate
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
        if (isset($request->api_token)) {
            $check_token = \Sudo\ApiUser\Models\ApiUser::where('api_token', $request->api_token)->where('status', 1)->first();
            if (!empty($check_token)) {
                return $next($request);
            }
        }
        return response()->json([
            'status' => 2,
            'message' => 'API check token Fail',
        ]);
    }
}
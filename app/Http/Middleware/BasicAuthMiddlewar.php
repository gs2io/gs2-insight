<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BasicAuthMiddlewar
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
        if (env("APP_BASIC_AUTH", false))
        {
            $username = $request->getUser();
            $password = $request->getPassword();

            if ($username == env("APP_BASIC_USER", 'user') && $password == env("APP_BASIC_PASSWORD", 'password')) {
                return $next($request);
            }

            abort(401, "Enter username and password.", [
                header('WWW-Authenticate: Basic realm="GS2-Insight"'),
                header('Content-Type: text/plain; charset=utf-8')
            ]);
        }

        return $next($request);
    }
}

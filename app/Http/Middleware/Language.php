<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Session;

class Language extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (isset($request->lang)) {
            $request->session()->put('locale', $request->lang);
        }
        if ($request->session()->has('locale')) {
            app()->setLocale($request->session()->get('locale'));
        }

        return $next($request);
    }
}

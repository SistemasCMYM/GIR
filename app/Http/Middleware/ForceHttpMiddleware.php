<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceHttpMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Si la solicitud viene por HTTPS, redirigir a HTTP
        if ($request->isSecure()) {
            $url = str_replace('https://', 'http://', $request->fullUrl());
            return redirect($url, 301);
        }

        return $next($request);
    }
}

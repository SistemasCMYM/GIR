<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TwoStepAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user has completed step 1 (NIT verification)
        if (!session('nit_verified') || !session('company_data')) {
            return redirect()->route('login.nit')->withErrors([
                'error' => 'Debes verificar el NIT de la empresa primero.'
            ]);
        }

        return $next($request);
    }
}

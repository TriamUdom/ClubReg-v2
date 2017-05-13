<?php

namespace App\Http\Middleware;

use Closure;

class President {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string|null              $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = NULL) {
        if (!$request->session()->has('president')) {
            return redirect()->guest('login');
        } elseif (!$request->session()->has('president')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return response()->view('errors.403');
            }
        }
        
        return $next($request);
    }
}

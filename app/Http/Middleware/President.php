<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

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
        if (!($request->session()->has('userid') && ($request->session()->get('usertype') == 'admin' || $request->session()->has('president')))) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                $request->session()->set('redirect_queue', $request->fullUrl());
                
                return redirect()->guest('login');
            }
        }
        
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Setting;
use Closure;

class DBMaintenance {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string|null              $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = NULL) {
        if (Setting::isUnderMaintenance()) {
            return response()->view('errors.503');
        }
        
        return $next($request);
    }
}

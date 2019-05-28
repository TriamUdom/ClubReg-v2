<?php

namespace App\Http\Middleware;

use Closure;

class Superuser {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string|null              $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = NULL) {
        if (!session()->has('president') AND !session()->has('student')) { //Not logged in
            return response('Unauthorized.', 401);
        }

        $superuserList = \App\Setting::getValue('superuser_list');

        if ($superuserList)
        {
            foreach($superuserList as $superuser)
            {
                if (session('userid') == $superuser)
                {
                    return $next($request);
                }
            }

            return response()->view('errors.403');
        }

        return response()->view('errors.400');
    }
}

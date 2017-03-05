<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class NoClubStudent {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if ($request->session()->has('student')) {
            $student = User::current();
            if ($student->hasClub()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response('Student already registered for club.', 403);
                } else {
                    return response()->view('errors.exception', ['title' => 'ไม่สามารถเข้าถึงหน้านี้', 'description' => 'นักเรียนมีชมรมแล้ว ไม่อนุญาตให้ลงทะเบียนซ้ำ']);
                }
            }
        } else {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('/');
            }
        }
        
        return $next($request);
    }
}

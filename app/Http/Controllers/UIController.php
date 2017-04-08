<?php

namespace App\Http\Controllers;


use App\Helper;
use App\User;
use Illuminate\Http\Request;

class UIController extends Controller {
    
    /*public function getCountdownTime() {
        if (Helper::shouldCountdown()) {
            return config('core.allow_register_time') - time();
        }
        
        return 0;
    }*/
    
    public function logout(Request $request) {
        $request->session()->flush();
        
        return redirect('/');
    }
}
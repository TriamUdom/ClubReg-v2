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
    
    public function temporary() {
        $file = file_get_contents('D:\Keen\Onedrive\Work2559 (M5)\TUCC\M.D. Prelist\Textified.txt');
        $artRoom = ['048', '049', '058', '059', '080', '081', '125', '126', '143', '144', '222', '223', '224'];
        $names = User::where('level', 6)->orderBy('room')->orderBy('firstname')->orderBy('lastname')->get();
        $foundCount = 0;
        foreach ($names as $user) {
            if (str_contains($file, $user->firstname . " \r\n " . $user->lastname)) {
                $foundCount++;
                if (in_array($user->room, $artRoom)) {
                    echo '<span style="color:red">' . $user->firstname . ' ' . $user->lastname . ' (' . $user->room . "; ART)</span>\n<br />";
                } else {
                    echo $user->firstname . ' ' . $user->lastname . ' (' . $user->room . ")\n<br />";
                }
            }
        }
        echo "\n\n<br /><br /> ============== COUNT: " . $foundCount;
    }
    
    public function logout(Request $request) {
        $request->session()->flush();
        
        return redirect('/');
    }
}
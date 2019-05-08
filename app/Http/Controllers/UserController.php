<?php
/*
 * Copyright (c) 2017 Siwat Techavoranant
 */

namespace App\Http\Controllers;

use App\Club;
use App\User;
use Illuminate\Http\Request;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;
use Log;

class UserController extends Controller {
    public function login(Request $request){
        $this->validate($request, [
            'student_id' => 'required|numeric',
            'citizen_id' => 'required|numeric'
        ]);

        $user = User::find($request->get('student_id'));

        if (is_null($user) OR $user->citizen_id != $request->get('citizen_id')){
            return redirect()->back()->withErrors(['citizen_id' => 'เลขประจำตัวนักเรียนหรือเลขประจำตัวประชาชนไม่ถูกต้อง']);
        }

        $userId = $user->student_id;
        $userName = $user->title . $user->firstname . ' ' . $user->lastname;

        if ($president = self::findClubIdOfPresident($userId)) {
            $request->session()->put('president', $president);
        } else {
            $request->session()->put('student', $userId);
        }

        $request->session()->put('username', $userName);
        $request->session()->put('userid', $userId);
        $request->session()->put('login_time', time());
        $request->session()->put('id_token', (string)$request->input('id_token'));

        Log::info('Logged in: ' . $userId, ['ip' => $request->ip(), 'name' => $userName, 'club' => $president ?? '']);

        return redirect()->intended()->with('notify', 'เข้าสู่ระบบแล้ว')->cookie('current', $userId, 60);
    }
    
    public function logout(Request $request) {
        $request->session()->flush();

        return redirect('/')->with('notify', 'ออกจากระบบแล้ว!')->cookie('current', false, 1);
    }
    
    protected function findClubIdOfPresident(string $userid) {
        $clubs = Club::where('user_id', 'LIKE', '%' . $userid . '%')->get();

        foreach ($clubs as $club) {
            if (in_array($userid, $club->user_id)) {
                return $club->id;
            }
        }
        
        return false;
    }
}
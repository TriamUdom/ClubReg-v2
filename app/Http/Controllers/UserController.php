<?php
/*
 * Copyright (c) 2017 Siwat Techavoranant
 */

namespace App\Http\Controllers;

use App\Club;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Log;

class UserController extends Controller {
    public function login(Request $request) {
        $this->validate($request, array(
            'student_id' => 'required',
            'password' => 'required'
        ));

        $user = User::where('student_id', '=', $request->get('student_id'))->first();

        if (is_null($user) or !\Hash::check($request->get('password'), $user->password)) {
            return redirect()->back()->withErrors(array('password' => 'เลขประจำตัวนักเรียนหรือรหัสผ่านไม่ถูกต้อง'));
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

        Log::info('Logged in: ' . $userId, array('ip' => $request->ip(), 'name' => $userName, 'club' => $president ?? ''));

        return redirect()->intended()->with('notify', 'เข้าสู่ระบบแล้ว')->cookie('current', $userId, 60);
    }

    public function register(Request $request) {
        $this->validate($request, array(
            'level' => array('required', Rule::in(array('4', '5', '6'))),
            'firstname' => 'required',
            'lastname' => 'required',
            'password' => 'required|string|regex:/^[\w-]*$/',
            'password_val' => 'required|string|regex:/^[\w-]*$/',
            'room' => 'required|numeric',
            'number' => 'required|numeric'
        ));

        if ($request->get('password') != $request->get('password_val')) {
            return redirect()->back()->withErrors(array('password_val' => 'ช่องยืนยันรหัสผ่านไม่ตรงกับช่องรหัสผ่าน'));
        }


        $this->validate($request, array('id' => 'required|numeric'));

        $user = User::where(array(
                array('firstname', '=', $request->get('firstname')),
                array('lastname', '=', $request->get('lastname')),
                array('level', '=', $request->get('level')),
                array('student_id', '=', $request->get('id')),
            ))->first();

        if ($president = self::findClubIdOfPresident($userId) and !($user->password == '')) {
            return redirect()->back()->withErrors(array('error' => 'ไม่สามารถยืนยันตัวตนได้ กรุณาติดต่อเพจ TUCMC'));
        }

        $user->room = $request->get('room');
        $user->number = $request->get('number');
        $user->password = \Hash::make($request->get('password'));
        $user->save();

        return view('register-finished', array('user' => $user));
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

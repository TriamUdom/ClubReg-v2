<?php
/*
 * Copyright (c) 2017 Siwat Techavoranant
 */

namespace App\Http\Controllers;

use App\Club;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;
use Log;

class UserController extends Controller {
    public function login(Request $request){
        $this->validate($request, [
            'student_id' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('student_id', '=', $request->get('student_id'))->first();

        if (is_null($user) OR !\Hash::check($request->get('password'), $user->password)){
            return redirect()->back()->withErrors(['password' => 'เลขประจำตัวนักเรียนหรือรหัสผ่านไม่ถูกต้อง']);
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

    public function register(Request $request){
        $this->validate($request, [
            'level' => ['required', Rule::in(['4', '5', '6'])],
            'firstname' => 'required',
            'lastname' => 'required',
            'password' => 'required|string|size:6|regex:/^[\w-]*$/',
            'password_val' => 'required|string|size:6|regex:/^[\w-]*$/',
            'room' => 'required|numeric',
            'number' => 'required|numeric'
        ]);

        if ($request->get('password') != $request->get('password_val')){
            return redirect()->back()->withErrors(['password_val' => 'ช่องยืนยันรหัสผ่านไม่ตรงกับช่องรหัสผ่าน']);
        }

        $level = $request->get('level');

        if ($level == 4){
            $user = User::where([
                ['firstname', '=', $request->get('firstname')],
                ['lastname', '=', $request->get('lastname')],
                ['level', '=', 4],
                ['room', '=', $request->get('room')],
                ['number', '=', $request->get('number')],
            ])->first();

            if (is_null($user)){
                return redirect()->back()->withErrors(['error' => 'ไม่สามารถยืนยันตัวต้นได้ กรุณาติดต่อหัวหน้างานกิจกรรมพัฒนาผู้เรียน ณ ตึก 50 ปี']);
            }

            if (!empty($user->password)){
                return redirect()->back()->withErrors(['error' => 'นักเรียนได้ยืนยันตัวตนและได้ตั้งรหัสผ่านใหม่แล้ว หากมีปัญหากรุณาติดต่อหัวหน้างานกิจกรรมพัฒนาผู้เรียน ณ ตึก 50 ปี']);
            }

            $user->password = \Hash::make($request->get('password'));
            $user->save();

            return view('register-finished', ['user' => $user, 'password' => $request->get('password')]);
        }
        else if ($level == 5 OR $level == 6){
            $this->validate($request, [
                'id' => 'required|numeric'
            ]);

            $user = User::where([
                ['firstname', '=', $request->get('firstname')],
                ['lastname', '=', $request->get('lastname')],
                ['level', '=', $level],
                ['student_id', '=', $request->get('id')],
            ])->first();

            if (is_null($user)){
                return redirect()->back()->withErrors(['error' => 'ไม่สามารถยืนยันตัวต้นได้ กรุณาติดต่อหัวหน้างานกิจกรรมพัฒนาผู้เรียน ณ ตึก 50 ปี']);
            }

            if (!empty($user->password)){
                return redirect()->back()->withErrors(['error' => 'นักเรียนได้ยืนยันตัวตนและได้ตั้งรหัสผ่านใหม่แล้ว หากมีปัญหากรุณาติดต่อหัวหน้างานกิจกรรมพัฒนาผู้เรียน ณ ตึก 50 ปี']);
            }

            $user->room = $request->get('room');
            $user->number = $request->get('number');
            $user->password = \Hash::make($request->get('password'));
            $user->save();

            return view('register-finished', ['user' => $user, 'password' => $request->get('password')]);
        }

        return redirect()->back()->withErrors(['level' => 'ระดับชั้นไม่ถูกต้อง']);
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
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
    public function redirectOpenID(Request $request) {
        $state = substr(sha1(microtime() . rand()), 0, 10);
        $nonce = substr(sha1(microtime() . rand()), 0, 10);
        $request->session()->put('openid_state', $state);
        $request->session()->put('openid_nonce', $nonce);
        
        return redirect(config('core.authorization_endpoint') . '/?scope=openid&response_type=id_token&client_id=' . config('core.client_id') . '&redirect_uri=' . urlencode(config('core.redirect_uri')) . '&nonce=' . $nonce . '&state=' . $state);
    }
    
    public function openidLogin(Request $request) {
        if (!$request->session()->has('openid_nonce')) {
            return view('errors.custom',
                ['title' => 'Error occured while authenticating', 'description' => 'ผู้ใช้ใช้เวลาในการยืนยันตัวตนมากเกินไป หรือทำการยืนยันตัวตนไม่ถูกต้อง กรุณาลองใหม่ (Expired State)']);
        } elseif ($request->session()->get('openid_state') != $request->input('state')) {
            return view('errors.custom', ['title' => 'Error occured while authenticating', 'description' => 'ผู้ใช้ทำการยืนยันตัวตนไม่ถูกต้อง กรุณาลองใหม่ (Invalid State)']);
        }
        
        $token = (new Parser())->parse((string)$request->input('id_token'));
        $vdata = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
        $vdata->setIssuer(config('core.openid_provider'));
        $signer = new Sha256();
        if (!$token->validate($vdata) && (config('app.env') != 'local')) {
            // Local environment time usually differs from TUSSO server.
            return view('errors.custom', ['title' => 'Error occured while authenticating', 'description' => 'ตัวตนผู้ใช้ไม่ถูกต้อง (Expired identity token)']);
        } elseif (!$token->verify($signer, config('core.client_secret'))) {
            return view('errors.custom', ['title' => 'Error occured while authenticating', 'description' => 'ตัวตนผู้ใช้ไม่ถูกต้อง (Invalid identity token)']);
        } elseif ($token->getClaim('nonce') != $request->session()->get('openid_nonce')) {
            return view('errors.custom', ['title' => 'Error occured while authenticating', 'description' => 'ตัวตนผู้ใช้ไม่ถูกต้อง (Invalid nonce)']);
        }
        
        $userId = $token->getClaim('id');
        $userName = $token->getClaim('name');
        
        // Lookup for student
        if ($student = self::findStudentByUserId($userId, $userName)) {
            $request->session()->put('student', $student->getId());
        }
        if ($president = self::findClubIdOfPresident($userId)) {
            $request->session()->put('president', $president);
        } elseif (empty($student)) {
            return view('errors.custom', ['title' => 'Access Denied', 'description' => 'ไม่อนุญาตให้เข้าใช้งาน']);
        }
        
        $request->session()->put('username', $userName);
        $request->session()->put('userid', $userId);
        $request->session()->put('login_time', time());
        $request->session()->put('id_token', (string)$request->input('id_token'));
        
        Log::info('Logged in: ' . $token->getClaim('id'), ['ip' => $request->ip(), 'name' => $token->getClaim('name'), 'club' => $president ?? '']);
        
        return redirect()->intended()->with('notify', 'เข้าสู่ระบบแล้ว')->cookie('current', $userId, 60);
    }
    
    public function logout(Request $request) {
        $idtoken = $request->session()->get('id_token');
        $request->session()->flush();
        
        if (!$request->has('local')) {
            return redirect(config('core.logout_endpoint') . '?id_token_hint=' . $idtoken . '&post_logout_redirect_uri=' . config('core.url'))->cookie('current', false, 1);
        } else {
            return redirect('/')->with('notify', 'ออกจากระบบแล้ว!')->cookie('current', false, 1);
        }
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
    
    /**
     * Get student model from wifi user data
     *
     * @param string $id
     * @param string $name
     * @return bool|User
     */
    protected function findStudentByUserId(string $id, string $name) {
        if (strlen($id) == 6 AND is_numeric(substr($id, 1))) {
            // TU79 or older generation : UserId is "s" followed by student id
            return User::where('student_id', substr($id, 1))->first() ?? false;
        } else {
            // if (strlen($id) == 10 AND is_numeric($id)) {
            // TU80 : UserId is TUID (something ICT department created)
            // so find student using name
            $namePart = explode(' ', $name);
            $lastPartCount = count($namePart) - 1;
            $user = User::where('firstname', $namePart[0])->where('lastname', $namePart[$lastPartCount])->first();
            if (empty($user) AND count($namePart) == 3) {
                // Support for middle name
                $user = User::where('firstname', $namePart[0] . $namePart[1])->where('lastname', $namePart[2])->first();
            }
            
            return $user ?? false;
        }
    }
}
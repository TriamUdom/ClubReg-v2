<?php

namespace App\Http\Controllers;

use App\Club;
use Illuminate\Http\Request;
use Log;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;

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
        
        // Check if user is president
        $clubs = Club::where('user_id', 'LIKE', '%' . $token->getClaim('id') . '%')->get();
        foreach ($clubs as $club) {
            if (in_array($token->getClaim('id'), $club->user_id)) {
                $president = $club->id;
                break;
            }
        }
        if (empty($president)) {
            return view('errors.custom', ['title' => 'Access Denied', 'description' => 'ไม่อนุญาตให้เข้าใช้งาน']);
        }
    
        $request->session()->put('president', $president);
        $request->session()->put('username', $token->getClaim('name'));
        $request->session()->put('userid', $token->getClaim('id'));
        $request->session()->put('login_time', time());
        $request->session()->put('id_token', (string)$request->input('id_token'));
        
        Log::info('President logged in: ' . $token->getClaim('id'), ['ip' => $request->ip(), 'name' => $token->getClaim('name'), 'club' => $president]);
        
        return redirect('/')->with('notify', 'เข้าสู่ระบบประธานชมรมเรียบร้อย');
    }
    
    public function logout(Request $request) {
        $idtoken = $request->session()->get('id_token');
        $request->session()->flush();
        
        if (!$request->has('local')) {
            return redirect(config('core.logout_endpoint') . '?id_token_hint=' . $idtoken . '&post_logout_redirect_uri=' . config('core.url'));
        } else {
            return redirect('/')->with('notify', 'ออกจากระบบแล้ว!');
        }
    }
}
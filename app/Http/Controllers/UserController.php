<?php

namespace App\Http\Controllers;

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
        $request->session()->put('login_as_admin', $request->has('admin'));
        
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
        if (in_array($token->getClaim('id'), config('core.admin'))) {
            $request->session()->put('usertype', 'admin');
            $ispresident = file_get_contents('https://clubs.triamudom.ac.th/api/president?id=' . $token->getClaim('id'));
            if ($ispresident != 'NOT_FOUND' && starts_with($ispresident, 'ก')) {
                $request->session()->put('president', $ispresident);
            }
        } else {
            $ispresident = file_get_contents('https://clubs.triamudom.ac.th/api/president?id=' . $token->getClaim('id'));
            if ($ispresident != 'NOT_FOUND' && starts_with($ispresident, 'ก')) {
                $request->session()->put('usertype', 'president');
                $request->session()->put('president', $ispresident);
            } else {
                return view('errors.custom', ['title' => 'Access Denied', 'description' => 'ไม่อนุญาตให้เข้าใช้งาน']);
            }
        }
        $request->session()->put('username', $token->getClaim('name'));
        $request->session()->put('userid', $token->getClaim('id'));
        $request->session()->put('usergroup', $token->getClaim('group'));
        $request->session()->put('login_time', time());
        
        Log::info('User logged in: ' . $token->getClaim('id'), ['ip' => $request->ip()]);
        
        if ($request->session()->has('redirect_queue')) {
            $redirect = $request->session()->get('redirect_queue');
            $request->session()->forget('redirect_queue');
            
            return redirect($redirect);
        }
        
        return redirect('/president')->with('notify', 'เข้าสู่ระบบเรียบร้อย');
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
<?php

return [
	'openid_provider' => 'https://accounts.triamudom.ac.th',
	"authorization_endpoint" => "https://accounts.triamudom.ac.th/openid/authorize",
	"token_endpoint" => "https://accounts.triamudom.ac.th/openid/token",
	"logout_endpoint" => "https://accounts.triamudom.ac.th/openid/logout",
	'client_id' => 'reg.clubs.triamudom.ac.th',
	'client_secret' => 'poramestza555+',
	'redirect_uri' => env('APP_URL', 'http://localhost') . '/openid_login',
	'url' => env('APP_URL', 'http://localhost'),
	
	'current_year' => '2559',
    'current_semester' => value(function () {
        $month = date('m');
        if ($month >= 5 AND $month <= 9) {
            return 1;
        } else {
            return 2;
        }
    }),
    
    'allow_register_time' => 1484835200, // Timestamp to start registering club, 0 if force allow, overriding "round" value. (used to show countdown)
    
    'round' => 'CONFIRM&AUDITION&WAR', // Enumerated types: WAITING, CONFIRM, CONFIRM&AUDITION, AUDITION, WAR, CLOSED (must be uppercase)

    'captcha_enable' => false
];
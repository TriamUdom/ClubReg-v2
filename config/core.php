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
	
	
	'admin' => array(
		'tuadmin', // Universal Admin Password
		's53783', // Siwat Techavoranant
		'songkiat.th',
		'poramest.mo',
		'manop_penrasamee',
		'yongyuth.ro',
		'sunan.pr',
	),
	
	'current_year' => '2559',
    
    'round' => 'CONFIRM', // Enumerated types: WAITING, CONFIRM, CONFIRM&AUDITION, AUDITION, WAR, CLOSED (must be uppercase)

    'captcha_enable' => false
];
<?php

Route::group(['middleware' => ['web']], function () {
    
    Route::get('/', function () {
        /*if (session()->has('userid')) {
            if (session()->has('student')) {
                return view('account');
            } elseif (session('usertype') == 'staff') {
                return view('admin-menu');
            } else {
                return response('Error in user session');
            }
        } else {
            return view('index');
        }*/
        return view('welcome');
    });
    
    Route::get('login', 'UserController@redirectOpenID');
    Route::post('openid_login', 'UserController@openidLogin');
    Route::get('logout', 'UserController@logout');
    
    if (config('app.debug')) {
        Route::get('phpinfo', function () {
            return response(phpinfo());
        });
        Route::get('session', function () {
            dump(session()->all());
        });
        Route::get('ip', function (\Illuminate\Http\Request $request) {
            return response(\App\Helper::getIPAddress($request) . ' (' . (\App\Helper::isIntranet($request, false) ? 'INTRA' : 'EXTERNAL') . ')');
        });
        Route::get('/view/{id}', function ($id) {
            if (!empty($id) && view()->exists($id)) {
                return view($id);
            } else {
                abort(404);
                
                return 'Not Found';
            }
        });
    }
});

Route::group(['middleware' => ['web', 'president']], function () {
    Route::get('fm', 'PresidentController@downloadFM3304');
});
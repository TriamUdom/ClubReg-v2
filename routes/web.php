<?php

Route::group(['middleware' => ['web']], function () {
    
    Route::get('/', function () {
        if (session()->has('student')) {
            return view('student.menu');
        }
        return view('student.login');
    });
    
    Route::post('login/student', 'StudentController@login');
    Route::get('logout', 'UIController@logout');
    
    // TUSSO integration
    //Route::get('login', 'UserController@redirectOpenID');
    //Route::post('openid_login', 'UserController@openidLogin');
    //Route::get('logout', 'UserController@logout');
    
    Route::get('club-list', function () { return view('clubs-status'); });
    
    if (config('app.debug')) {
        Route::get('phpinfo', function () {
            return response(phpinfo());
        });
        Route::get('session', function () {
            dump(session()->all());
        });
        Route::get('ip', function (\Illuminate\Http\Request $request) {
            return response(\App\Helper::getIPAddress($request));
        });
        Route::get('/view/{id}', function ($id) {
            if (!empty($id) && view()->exists($id)) {
                return view($id);
            } else {
                abort(404);
                
                return 'Not Found';
            }
        });
        Route::get('temp', 'UIController@temporary');
    }
});

Route::group(['middleware' => ['web', 'student']], function () {
    Route::post('club-register/old', 'StudentController@confirmOldClub');
    Route::post('club-register/audition', 'StudentController@applyForAudition');
    Route::post('club-register/apply', 'StudentController@joinClub');
    Route::post('club-register/confirm-audition', 'StudentController@confirmAudition');
    Route::get('club-register/{club}', function (\App\Club $club) {
        return view('student.club-confirm', ['club' => $club]);
    });
});

Route::group(['middleware' => ['web', 'president']], function () {
    Route::get('fm', 'PresidentController@downloadFM3304');
});
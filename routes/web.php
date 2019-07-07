<?php

Route::group(['middleware' => ['web']], function () {
    Route::get('/', function () {
        if (session()->has('president') and !session()->has('student')) {
            return view('president.menu');
        } elseif (session()->has('student')) {
            $user = \App\User::current();
            if ($user->hasClub()) {
                return view('student.finished', ['user' => $user]);
            } else {
                return view('student.menu', ['user' => $user]);
            }
        }
        return view('student.login');
    });

    Route::get('login', function () {
        return view('login');
    });
    Route::post('login', 'UserController@login');
    Route::get('logout', 'UserController@logout');
    Route::get('register', function () {
        return view('register');
    });
    Route::post('register', 'UserController@register');

    Route::get('info', function () {
        return view('info');
    });
    Route::get('clubinfo', function () {
        return view('detail');
    });
    Route::get('contact', function () {
        return view('contact');
    });

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
    Route::post('confirm', 'StudentController@verifyClub');
    Route::get('invalidInfo', function () {
        return view('student.edit-info');
    });
    Route::post('invalidInfo', 'StudentController@saveInvalidInfo');
});

Route::group(['middleware' => ['web', 'superuser']], function () {
    Route::get('settings', function () {
        return view('superuser.settings');
    });
    Route::get('setClub', function () {
        return view('superuser.addclub');
    });
    Route::post('settings', 'SuperuserController@changeSettings');
    Route::post('setClub', 'SuperuserController@setClub');
});

Route::group(['middleware' => ['web', 'president'], 'prefix' => 'president'], function () {
    Route::get('president', function () {
        return view('president.menu');
    });
    Route::get('members', function () {
        return view('president.members');
    });
    Route::get('fm3304', 'PresidentController@downloadFM3304');
    Route::get('audition', function () {
        return view('president.audition');
    });
    Route::post('audition', 'PresidentController@manageAudition');
    
    Route::get('settings', function () {
        return view('president.settings');
    });
    Route::post('settings', 'PresidentController@saveSettings');
});

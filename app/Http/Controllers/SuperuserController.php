<?php
/*
 * Copyright (c) 2017 Siwat Techavoranant
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuperuserController extends Controller
{
    public function changeSettings(Request $request)
    {
        try {
            $setting = \App\Setting::where('id', 'maintenance')->firstOrFail();
            $setting->value = $request->get('maintenance') ? 1 : 0;
            $setting->save();

            $setting = \App\Setting::where('id', 'superuser_list')->firstOrFail();
            $setting->value = array_map('trim', explode(',', $request->get('superuser_list')));
            $setting->save();

            $setting = \App\Setting::where('id', 'round')->firstOrFail();
            $setting->value = $request->get('round');
            $setting->save();

            return redirect('/');
        } catch (\Exception $ex) {
            return redirect()->back()->with('notify', 'ข้อมูลไม่ถูกต้อง');
        }
    }

    public function setClub(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
            'club' => 'required',
            'reason' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 100]);
        }

        $student = \App\User::find($request->get('student_id'));

        if (is_null($student)) {
            return response()->json(['code' => 100]);
        }

        if ($request->get('club') == 'none') {
            $student->club_id = '';
            $student->reason = '';
        } else {
            $club = \App\Club::find($request->get('club'));

            if (is_null($club)) {
                return response()->json(['code' => 100]);
            }

            $student->club_id = $request->get('club');
            $student->reason = $request->get('reason');
        }

        $student->save();

        return response()->json(['code' => 200]);
    }
}

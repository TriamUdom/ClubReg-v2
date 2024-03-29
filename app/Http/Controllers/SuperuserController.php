<?php
/*
 * Copyright (c) 2017 Siwat Techavoranant
 */

namespace App\Http\Controllers;

use App\Club;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuperuserController extends Controller {
    public function changeSettings(Request $request) {
        try {
            $setting = Setting::where('id', 'maintenance')->firstOrFail();
            $setting->value = $request->get('maintenance') ? 1 : 0;
            $setting->save();

            $setting = Setting::where('id', 'superuser_list')->firstOrFail();
            $setting->value = array_map('trim', explode(',', $request->get('superuser_list')));
            $setting->save();

            $setting = Setting::where('id', 'round')->firstOrFail();
            $setting->value = $request->get('round');
            $setting->save();

            return redirect('/');
        } catch (\Exception $ex) {
            return redirect()->back()->with('notify', 'ข้อมูลไม่ถูกต้อง');
        }
    }

    public function setClub(Request $request) {
        $validator = Validator::make($request->all(), array(
            'student_id' => 'required',
            'club' => 'required',
            'reason' => 'required'
        ));

        if ($validator->fails()) {
            return response()->json(array('code' => 100));
        }

        $student = User::find($request->get('student_id'));

        if (is_null($student)) {
            return response()->json(array('code' => 100));
        }

        if ($request->get('club') == 'none') {
            $student->club_id = '';
            $student->reason = '';
        } else {
            $club = Club::find($request->get('club'));

            if (is_null($club)) {
                return response()->json(array('code' => 100));
            }

            $student->club_id = $request->get('club');
            $student->reason = $request->get('reason');
        }

        $student->save();

        return response()->json(array('code' => 200));
    }

    public function findStudent(Request $request) {
        $validator = Validator::make($request->all(), array(
            'student_id' => 'required',
        ));

        if ($validator->fails()) {
            return response()->json(array('code' => 100));
        }

        $student = User::find($request->get('student_id'));

        if($student) {
            return response()->json(array('code' => 200, 'name' => $student->title . $student-> firstname . "  " . $student->lastname, 'room' => $student->room, 'level' => $student->level, 'club' => \App\Club::findClubName($student->club_id)));
        }

        return response()->json(array('code' => 100));
    }
}

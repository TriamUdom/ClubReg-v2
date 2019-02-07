<?php
/*
 * Copyright (c) 2017 Siwat Techavoranant
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class SuperuserController extends Controller {

    public function settingsPage(Request $request){
        if (!session()->has('president') AND !session()->has('student')) { //Not logged in
            return view('errors.custom', ['title' => 'Access Denied', 'description' => 'ไม่อนุญาตให้เข้าใช้งาน']);
        }

        $superuserList = \App\Setting::getValue('superuser_list');

        if ($superuserList)
        {
            foreach($superuserList as $superuser)
            {
                if (session()->get('userid') == $superuser)
                {
                    return view('superuser.settings');
                }
            }

            return view('errors.custom', ['title' => 'Access Denied', 'description' => 'ไม่อนุญาตให้เข้าใช้งาน']);
        }

        return view('errors.custom', ['title' => 'Invalid Setup', 'description' => 'การตั้งค่าของระบบไม่ถูกต้อง']);
    }

    public function changeSettings(Request $request){
        /*
        if (!session()->has('president') AND !session()->has('student')) { //Not logged in
            return view('errors.custom', ['title' => 'Access Denied', 'description' => 'ไม่อนุญาตให้เข้าใช้งาน']);
        }

        $superuserList = \App\Setting::getValue('superuser_list');

        if ($superuserList)
        {
            foreach($superuserList as $superuser)
            {
                if (session()->get('userid') == $superuser)
                {
                    try {
                        $setting = \App\Setting::where('id', 'maintenance')->firstOrFail();
                        $setting->value = json_encode($request->get('maintenance'));
                        $setting->save();

                        $setting = \App\Setting::where('id', 'superuser_list')->firstOrFail();
                        $setting->value = json_encode(array_map('trim', explode(',', $request->get('superuser_list'))));
                        $setting->save();

                        $setting = \App\Setting::where('id', 'round')->firstOrFail();
                        $setting->value = json_encode($request->get('round'));
                        $setting->save();

                        return redirect('/');
                    }
                    catch(\Exception $ex)
                    {
                        return redirect()->back()->with('notify', 'ข้อมูลไม่ถูกต้อง');
                    }
                }
            }

            return view('errors.custom', ['title' => 'Access Denied', 'description' => 'ไม่อนุญาตให้เข้าใช้งาน']);
        }

        return view('errors.custom', ['title' => 'Invalid Setup', 'description' => 'การตั้งค่าของระบบไม่ถูกต้อง']);
        */
    }

}
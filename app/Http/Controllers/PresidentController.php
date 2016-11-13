<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class PresidentController extends Controller {
    public function downloadFM3304 (Request $request) {
        if ($request->session()->has('president')) {
            return response()->download(\App\Club::find($request->session()->get('president'))->createFM3304(2))->deleteFileAfterSend(true);
        } else {
            return response('NO_PRESIDENT_CLUB');
        }
    }
}
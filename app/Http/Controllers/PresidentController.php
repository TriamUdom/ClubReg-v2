<?php

namespace App\Http\Controllers;


use App\Club;
use Illuminate\Http\Request;

class PresidentController extends Controller {
    public function downloadFM3304 (Request $request) {
        if ($request->session()->has('president') || $request->session()->get('usertype') == 'admin') {
            $club = $request->session()->get('president');
            return response()->download(Club::find($club)->createFM3304(2))->deleteFileAfterSend(true);
        } else {
            return response('NO_PRESIDENT_CLUB');
        }
    }
}
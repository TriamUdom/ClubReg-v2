<?php

namespace App\Http\Controllers;


use App\Audition;
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
    
    public function manageAudition (Request $request) {
        $this->validate($request, [
            'audition' => 'required|exists:auditions,id', // Audition Request ID
            'action' => 'required|in:pass,fail'
        ]);
        
        $club = Club::currentPresident();
        /** @var Audition $audition */
        $audition = Audition::find($request->input('audition'));
        
        if ($club->id != $audition->club_id) {
            return response()->view('errors.exception', ['title' => 'Bad Request', 'description' => 'รหัสการออดิชั่นไม่สัมพันธ์กับชมรม']);
        } else {
            switch (strtolower($request->input('action'))) {
                case 'pass':
                    $audition->updateStatus(Audition::Status_Passed);
                    break;
                case 'fail':
                    $audition->updateStatus(Audition::Status_Failed);
                    break;
                default:
                    return response()->view('errors.exception', ['title' => 'Bad Request', 'description' => 'คำสั่งไม่ถูกต้อง']);
            }
            // @todo Return to previous page
        }
    }
}
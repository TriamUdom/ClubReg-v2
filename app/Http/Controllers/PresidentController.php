<?php
/*
 * Copyright (c) 2017 Siwat Techavoranant
 */

namespace App\Http\Controllers;

use App\Audition;
use App\Club;
use Illuminate\Http\Request;

class PresidentController extends Controller {
    public function downloadFM3304(Request $request) {
        /** @var $club Club */
        $club = Club::find($request->session()->get('president'));
        
        return response()->download($club->createFM3304(config('core.current_semester')))->deleteFileAfterSend(true);
    }
    
    public function manageAudition(Request $request) {
        if (!\App\Helper::isRound(\App\Helper::Round_Audition)) {
            return response()->view('errors.exception', array('title' => 'Not Yet', 'description' => 'ไม่สามารถแก้ไขผลการออดิชั่นในขณะนี้'));
        }

        $this->validate($request, array(
            'audition' => 'required|exists:auditions,id', // Audition Request ID
            'action' => 'required|in:pass,fail'
        ));
        
        $club = Club::currentPresident();
        /** @var Audition $audition */
        $audition = Audition::find($request->get('audition'));
        
        if ($club->id != $audition->club_id) {
            return response()->json(array('code' => 100));
        } elseif ($audition->status == Audition::Status_Canceled or $audition->status == Audition::Status_Joined or $audition->status == Audition::Status_Rejected) {
            return response()->json(array('code' => 100));
        } else {
            switch (strtolower($request->get('action'))) {
                case 'pass':
                    $audition->updateStatus(Audition::Status_Passed);
                    break;
                case 'fail':
                    $audition->updateStatus(Audition::Status_Failed);
                    break;
                default:
                    return response()->json(array('code' => 100));
            }

            return response()->json(array('code' => 200));
        }
    }
    
    public function saveSettings(Request $request) {
        $club = Club::currentPresident();
        if ($club->update($request->all())) {
            return redirect('/')->with('notify', 'บันทึกแล้ว');
        }
        
        return response()->view('errors.exception', array('title' => 'Unexpected Error', 'description' => 'ผิดพลาด! โปรดลองใหม่หรือติดต่อผู้ดูแลระบบ'));
    }
}

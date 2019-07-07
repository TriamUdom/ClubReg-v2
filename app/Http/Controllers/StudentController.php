<?php
/*
 * Copyright (c) 2017 Siwat Techavoranant
 */

namespace App\Http\Controllers;

use App\Audition;
use App\Club;
use App\Exceptions\TransactionException;
use App\Helper;
use App\User;
use DB;
use Illuminate\Http\Request;
use Throwable;

class StudentController extends Controller {
    /**
     * (Round CONFIRM) Register for old club
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function confirmOldClub(Request $request) {
        $this->validate($request, array(
            'club' => 'required|exists:clubs,id' // Club ID, as confirmation
        ));
        
        if (!Helper::isRound(Helper::Round_Confirm)) {
            return response()->view('errors.exception', array('title' => 'ไม่อนุญาต', 'description' => 'ขณะนี้ไม่อนุญาตให้ลงทะเบียน'));
        }
        
        $student = User::current();
        
        if ($student->getPreviousClub() == $request->input('club') and !$student->hasClub()) {
            /** @var Club $club */
            $club = Club::find($student->getPreviousClub());
            if ($club->isAvailableForConfirm() and $club->isAvailableForLevel($student->level)) {
                if ($student->registerClub($club->id, User::RegisterType_ExistingMember)) {
                    return redirect('/')->with('notify', 'ลงทะเบียนชมรมแล้ว');
                } else {
                    return response()->view('errors.exception', array('title' => 'ไม่สามารถลงทะเบียนชมรม', 'code' => date(DATE_ISO8601)));
                }
            } else {
                return response()->view('errors.exception', array('title' => 'ไม่สามารถลงทะเบียนชมรม', 'description' => 'ชมรมที่นักเรียนต้องการเต็มแล้ว'));
            }
        }
        
        return response()->view('errors.exception', array('title' => 'ไม่สามารถลงทะเบียนชมรม', 'description' => 'รหัสยืนยันไม่ถูกต้อง หรือนักเรียนมีชมรมแล้ว'));
    }

    /**
     * (Round AUDITION) Apply for audition
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function applyForAudition(Request $request) {
        $this->validate($request, array(
            'club' => 'required|exists:clubs,id' // Club ID
        ));
        
        if (!Helper::isRound(Helper::Round_Register)) { //Audition is only allowed during round 1
            return response()->view('errors.exception', array('title' => 'ไม่อนุญาต', 'description' => 'ขณะนี้ไม่อนุญาตให้ลงทะเบียน'));
        }
        
        $student = User::current();
        
        if (!Club::find($request->input('club'))->isAvailableForLevel($student->level)) {
            return response()->view('errors.exception', array('title' => 'ไม่สามารถลงทะเบียนออดิชั่น', 'description' => 'ชมรมรับนักเรียนเต็มแล้ว'));
        } elseif (Audition::apply($student->student_id, $request->input('club'))) {
            return redirect('/')->with('notify', 'ลงทะเบียนออดิชั่นชมรมแล้ว');
        } else {
            return response()->view('errors.exception', array('title' => 'ไม่สามารถลงทะเบียนออดิชั่น', 'description' => 'มีการออดิชั่นชมรมนี้อยู่แล้ว'));
        }
    }

    public function verifyClub(Request $request) {
        $student = User::current();
        if ($request->input('action') == 'confirm') {
            $student->confirmed=true;
            $student->save();

            return redirect('/')->with('notify', 'ยืนยันข้อมูลเรียบร้อยแล้ว');
        } elseif ($request->input('action') == 'invalid') {
            return redirect('/invalidInfo');
        }

        return response()->view('errors.exception', array('title' => 'ไม่สามารถบันทึกข้อมูลได้'));
    }

    public function saveInvalidInfo(Request $request) {
        $student = User::current();
        $problem = new \App\Problem;

        if ($request->input('action') !== 'submit') {
            if (\App\Problem::where('student_id', '=', $student->student_id)->count() > 0) {
                return response()->view('errors.exception', array('title' => 'ไม่สามารถบันทึกข้อมูลได้', 'description' => 'คุณได้ทำการบันทึกข้อมูลไปแล้ว'));
            } else {
                $problem->student_id=$student->student_id;
                $problem->real_club_id=$request->get('club');
                $problem->save();

                return redirect('/')->with('notify', 'บันทึกข้อมูลเรียบร้อยแล้ว');
            }
        }

        return response()->view('errors.exception', array('title' => 'ไม่สามารถบันทึกข้อมูลได้'));
    }
    
    /**
     * Confirm/Reject to join club which has passed audition
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws TransactionException
     */
    public function confirmAudition(Request $request) {
        $this->validate($request, array(
            'audition' => 'required|exists:auditions,id', // Audition Request ID
            'action' => 'required|in:join,reject'
        ));
        
        if (!Helper::isRound(Helper::Round_Audition)) { //Can only confirm during round 1.5
            return response()->view('errors.exception', array('title' => 'ไม่อนุญาต', 'description' => 'ขณะนี้ไม่อนุญาตให้ลงทะเบียน'));
        }
        
        $student = User::current();
        
        if ($student->hasClub()) {
            return response()->view('errors.exception', array('title' => 'นักเรียนลงทะเบียนชมรมแล้ว', 'description' => 'ไม่สามารถเข้าชมรมได้'));
        } elseif ($audition = Audition::find($request->input('audition'))) {
            /** @var Audition $audition */
            if ($audition->student_id == $student->student_id) {
                if ($request->input('action') == 'cancel' and $audition->status == Audition::Status_Awaiting) {
                    $audition->updateStatus(Audition::Status_Canceled);
                    
                    return redirect('/')->with('notify', 'ยกเลิกการสมัครเข้าชมรมแล้ว');
                } elseif ($request->input('action') == 'reject' and $audition->status == Audition::Status_Passed) {
                    $audition->updateStatus(Audition::Status_Rejected);
                    
                    return redirect('/')->with('notify', 'ปฏิเสธการเข้าชมรมแล้ว');
                } elseif ($request->input('action') == 'join' and $audition->status == Audition::Status_Passed) {
                    if ($audition->club->isAvailableForLevel($student->level)) {
                        try {
                            DB::transaction(function () use ($student, $audition) {
                                $audition->updateStatus(Audition::Status_Joined);
                                $student->registerClub($audition->club_id, User::RegisterType_Audition);
                            });
                            
                            return redirect('/')->with('notify', 'เข้าร่วมชมรมแล้ว');
                        } catch (Throwable $e) {
                            throw new TransactionException('Unable to join club (Audition)');
                        }
                    } else {
                        return response()->view('errors.exception', array('title' => 'ไม่สามารถยืนยันการเข้าชมรม', 'description' => 'ชมรมเต็มแล้ว'));
                    }
                } else {
                    return response()->view('errors.exception', array('title' => 'ไม่สามารถยืนยันการเข้าชมรม', 'description' => 'คำสั่งไม่ถูกต้อง'));
                }
            } else {
                return response()->view('errors.exception', array('title' => 'ไม่สามารถยืนยันการเข้าชมรม', 'description' => 'รหัสการออดิชั่นไม่ถูกต้อง'));
            }
        } else {
            return response()->view('errors.exception', array('title' => 'ไม่สามารถยืนยันการเข้าชมรม', 'description' => 'ไม่พบการออดิชั่น'));
        }
    }
    
    /**
     * Register for supplied club instantly (For War or Glean)
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function joinClub(Request $request) {
        if (!Helper::isRound(Helper::Round_Register) and !Helper::isRound(Helper::Round_Glean)) {
            return response()->view('errors.exception', array('title' => 'ไม่อนุญาต', 'description' => 'ขณะนี้ไม่อนุญาตให้ลงทะเบียน'));
        }
        
        $this->validate($request, array(
            'club' => 'required|exists:clubs,id' // Club ID
        ));
        
        $student = User::current();

        if (Helper::isRound(Helper::Round_Glean) && count($student->getAuditions()) == 0) {
            return response()->view('errors.exception', array('title' => 'ไม่อนุญาต', 'description' => 'คุณไม่มีสิทธิ์ลงทะเบียนในรอบ 2'));
        }
        
        if ($student->hasClub()) {
            return response()->view('errors.exception', array('title' => 'นักเรียนลงทะเบียนชมรมแล้ว', 'description' => 'ไม่สามารถเข้าชมรมได้'));
        } elseif (!Club::find($request->input('club'))->isAvailableForLevel($student->level)) {
            return response()->view('errors.exception', array('title' => 'ชมรมเต็มแล้ว', 'description' => 'ไม่สามารถเข้าชมรมได้'));
        } else {
            $student->registerClub($request->input('club'), User::RegisterType_War);
            
            return redirect('/')->with('notify', 'ลงทะเบียนชมรมแล้ว');
        }
    }
}

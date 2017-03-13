<?php

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
    public function login(Request $request) {
        if (config('core.captcha_enable')) {
            $this->validate($request, ['g-recaptcha-response' => 'required|recaptcha']);
        }
        $this->validate($request, [
            'citizen_id' => 'required|digits:13', // Citizen Identification Number
            'student_id' => 'nullable|digits:5' // Birthday (DD/MM/YYYY)
        ]);
        
        if ($claimedUser = User::find($request->input('citizen_id'))) {
            /** @var $claimedUser User */
            if ($claimedUser->student_id == $request->input('student_id') OR (empty($claimedUser->student_id) AND $request->input('student_id') == '11111')) {
                // Authenticated
                $request->session()->put('student', $claimedUser->citizen_id);
    
                return redirect()->intended('/');
            } else {
                return back()->withErrors('รหัสประจำตัวประชาชนหรือรหัสนักเรียนไม่ถูกต้อง');
            }
        } else {
            return back()->withErrors('รหัสประจำตัวประชาชนหรือรหัสนักเรียนไม่ถูกต้อง');
        }
    }
    
    /**
     * (Round CONFIRM) Register for old club
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function confirmOldClub(Request $request) {
        $this->validate($request, [
            'club' => 'required|size:6|exists:clubs,id' // Club ID, as confirmation
        ]);
        
        if (!Helper::isRound(Helper::Round_Confirm)) {
            return response()->view('errors.exception', ['title' => 'ไม่อนุญาต', 'description' => 'ขณะนี้ไม่อนุญาตให้ลงทะเบียน']);
        }
        
        $student = User::current();
        
        if ($student->getPreviousClub() == $request->input('club') AND !$student->hasClub()) {
            /** @var Club $club */
            $club = Club::find($student->getPreviousClub());
            if ($club->isAvailable()) {
                if ($student->registerClub($club->id, User::RegisterType_ExistingMember)) {
                    return redirect('/')->with('notify', 'ลงทะเบียนชมรมแล้ว');
                } else {
                    return response()->view('errors.exception', ['title' => 'ไม่สามารถลงทะเบียนชมรม', 'code' => date(DATE_ISO8601)]);
                }
            } else {
                return response()->view('errors.exception', ['title' => 'ไม่สามารถลงทะเบียนชมรม', 'description' => 'ชมรมที่นักเรียนต้องการเต็มแล้ว']);
            }
        }
        
        return response()->view('errors.exception', ['title' => 'ไม่สามารถลงทะเบียนชมรม', 'description' => 'รหัสยืนยันไม่ถูกต้อง หรือนักเรียนมีชมรมแล้ว']);
    }
    
    /**
     * (Round AUDITION) Apply for audition
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function applyForAudition(Request $request) {
        $this->validate($request, [
            'club' => 'required|size:6|exists:clubs,id' // Club ID
        ]);
        
        if (!Helper::isRound(Helper::Round_Audition)) {
            return response()->view('errors.exception', ['title' => 'ไม่อนุญาต', 'description' => 'ขณะนี้ไม่อนุญาตให้ลงทะเบียน']);
        }
        
        $student = User::current();
        
        if (Audition::apply($student->citizen_id, $request->input('club'))) {
            return redirect('/')->with('notify', 'ลงทะเบียนออดิชั่นชมรมแล้ว');
        } else {
            return response()->view('errors.exception', ['title' => 'ไม่สามารถลงทะเบียนออดิชั่น', 'description' => 'มีการออดิชั่นชมรมนี้อยู่แล้ว']);
        }
    }
    
    /**
     * (Round AUDITION) Confirm to join club which has passed audition
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws TransactionException
     */
    public function confirmAudition(Request $request) {
        $this->validate($request, [
            'audition' => 'required|exists:auditions,id' // Audition Request ID
        ]);
        
        if (!Helper::isRound(Helper::Round_Audition)) {
            return response()->view('errors.exception', ['title' => 'ไม่อนุญาต', 'description' => 'ขณะนี้ไม่อนุญาตให้ลงทะเบียน']);
        }
        
        $student = User::current();
        
        if ($student->hasClub()) {
            return response()->view('errors.exception', ['title' => 'นักเรียนลงทะเบียนชมรมแล้ว', 'description' => 'ไม่สามารถเข้าชมรมได้']);
        } elseif ($audition = Audition::find($request->input('audition'))) {
            /** @var Audition $audition */
            if ($audition->citizen_id == $student->citizen_id) {
                if ($audition->club->isAvailable()) {
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
                    return response()->view('errors.exception', ['title' => 'ไม่สามารถยืนยันการเข้าชมรม', 'description' => 'ชมรมเต็มแล้ว']);
                }
            } else {
                return response()->view('errors.exception', ['title' => 'ไม่สามารถยืนยันการเข้าชมรม', 'description' => 'รหัสการออดิชั่นไม่ถูกต้อง']);
            }
        } else {
            return response()->view('errors.exception', ['title' => 'ไม่สามารถยืนยันการเข้าชมรม', 'description' => 'ไม่พบการออดิชั่น']);
        }
    }
    
    /**
     * (Round AUDITION) Reject to join club which has passed audition
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function rejectAudition(Request $request) {
        $this->validate($request, [
            'audition' => 'required|exists:auditions,id' // Audition Request ID
        ]);
        
        if (!Helper::isRound(Helper::Round_Audition)) {
            return response()->view('errors.exception', ['title' => 'ไม่อนุญาต', 'description' => 'ขณะนี้ไม่อนุญาตให้ลงทะเบียน']);
        }
        
        $student = User::current();
        
        if ($audition = Audition::find($request->input('audition'))) {
            /** @var Audition $audition */
            if ($student->citizen_id == $audition->citizen_id) {
                $audition->updateStatus(Audition::Status_Rejected);
                return redirect('/')->with('notify', 'ปฏิเสธการเข้าชมรมแล้ว');
            } else {
                return response()->view('errors.exception', ['title' => 'ไม่สามารถปฏิเสธการเข้าชมรม', 'description' => 'รหัสการออดิชั่นไม่ถูกต้อง']);
            }
        } else {
            return response()->view('errors.exception', ['title' => 'ไม่สามารถปฏิเสธการเข้าชมรม', 'description' => 'ไม่พบการออดิชั่น']);
        }
    }
    
    /**
     * (Round WAR) Register for supplied club
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function joinClub(Request $request) {
        if (!Helper::isRound(Helper::Round_War)) {
            return response()->view('errors.exception', ['title' => 'ไม่อนุญาต', 'description' => 'ขณะนี้ไม่อนุญาตให้ลงทะเบียน']);
        }
        
        $this->validate($request, [
            'club' => 'required|size:6|exists:clubs,id' // Club ID
        ]);
        
        $student = User::current();
        
        if ($student->hasClub()) {
            return response()->view('errors.exception', ['title' => 'นักเรียนลงทะเบียนชมรมแล้ว', 'description' => 'ไม่สามารถเข้าชมรมได้']);
        } elseif (!Club::find($request->input('club'))->isAvailable()) {
            return response()->view('errors.exception', ['title' => 'ชมรมเต็มแล้ว', 'description' => 'ไม่สามารถเข้าชมรมได้']);
        } else {
            $student->registerClub($request->input('club'), User::RegisterType_War);
            
            return redirect('/')->with('notify', 'ลงทะเบียนชมรมแล้ว');
        }
    }
}
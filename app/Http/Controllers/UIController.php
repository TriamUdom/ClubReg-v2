<?php

namespace App\Http\Controllers;


use App\Helper;
use App\User;
use Illuminate\Http\Request;

class UIController extends Controller {
    /*public function getCountdownTime() {
        if (Helper::shouldCountdown()) {
            return config('core.allow_register_time') - time();
        }
        
        return 0;
    }*/
    
    public function temporary() {
        $file = file_get_contents('D:\Keen\Onedrive\Work2559 (M5)\TUCC\M.D. Prelist\Textified.txt');
        $artRoom = ['048', '049', '058', '059', '080', '081', '125', '126', '143', '144', '222', '223', '224'];
        $names = User::where('level', 6)->orderBy('room')->orderBy('firstname')->orderBy('lastname')->get();
        $foundCount = 0;
        foreach ($names as $user) {
            if (str_contains($file, $user->firstname . " \r\n " . $user->lastname)) {
                $foundCount++;
                if (in_array($user->room, $artRoom)) {
                    echo '<span style="color:red">' . $user->firstname . ' ' . $user->lastname . ' (' . $user->room . "; ART)</span>\n<br />";
                } else {
                    echo $user->firstname . ' ' . $user->lastname . ' (' . $user->room . ")\n<br />";
                }
            }
        }
        echo "\n\n<br /><br /> ============== COUNT: " . $foundCount;
    }
    
    public function temp2() {
        $students = \DB::table('temp_m6')->where('response', 'LIKE', '%เนเธเธเนเธเธ%')->get();
        foreach ($students as $student) {
            //if (empty($student->response)) {
                $ch = curl_init();
                
                curl_setopt($ch, CURLOPT_URL, "http://www9.si.mahidol.ac.th/result_cotmes_final_2560.asp");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "mode=search&txtIdCard=" . $student->citizen_id);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                $server_output = curl_exec($ch);
                
                curl_close($ch);
            /*} else {
                $server_output = $student->response;
            }*/
            if (empty($server_output)) {
                echo 'Empty response!!!';
                break;
            } else {
                \DB::table('temp_m6')->where('citizen_id', $student->citizen_id)->update([
                    'response' => (strlen($server_output) < 3000) ? 'NO' : self::delete_all_between('<head>', '</head>', self::tis620_to_utf8($server_output))
                ]);
                echo $student->firstname . ' ' . $student->lastname . ' (' . $student->room . ') Response recorded!<br />';
            }
        }
    }
    
    private static function delete_all_between($beginning, $end, $string) {
        $beginningPos = strpos($string, $beginning);
        $endPos = strpos($string, $end);
        if ($beginningPos === false || $endPos === false) {
            return $string;
        }
        
        $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);
        
        return str_replace($textToDelete, '', $string);
    }
    
    private static function tis620_to_utf8($text) {
        $utf8 = "";
        for ($i = 0; $i < strlen($text); $i++) {
            $a = substr($text, $i, 1);
            $val = ord($a);
            
            if ($val < 0x80) {
                $utf8 .= $a;
            } elseif ((0xA1 <= $val && $val < 0xDA) || (0xDF <= $val && $val <= 0xFB)) {
                $unicode = 0x0E00 + $val - 0xA0;
                $utf8 .= chr(0xE0 | ($unicode >> 12));
                $utf8 .= chr(0x80 | (($unicode >> 6) & 0x3F));
                $utf8 .= chr(0x80 | ($unicode & 0x3F));
            }
        }
        
        return $utf8;
    }
    
    public function tempFilter() {
        $students = \DB::table('temp_m6')->where('response', '!=', 'NO')->whereNull('sum')->get();
        foreach ($students as $student) {
            $response = $student->response;
            
            $aR = explode('<span class="style28">', $response);
            $aG = array();
            foreach ($aR as $a) {
                if (strlen($a) < 1000) { // If not too long
                    $g = trim(str_replace([
                        '</span></td>',
                        '<td height="29">',
                        '<td>',
                        '</tr>',
                        '<tr align="center"  class="style22">',
                        '<B>ปรับสัดส่วน</B></td>',
                        '<B>เนเธเนเธเธเนเธเธเนเธเนเธเธเนเธเธเนเธเนเธเธเนเธเนเธเธเนเธ</B></td>',
                        '<B>เธเธฃเธฑเธเธชเธฑเธเธชเนเธงเธ</B></td>',
                        '<font color="#FF0000">',
                        '</font>'
                    ], '', $a));
                    if (is_numeric($g)) {
                        $aG [] = (float)$g;
                    } else {
                        break;
                    }
                }
            }
            if (!empty($aG)) {
                
                $bR = explode('<td height="29" colspan="5" class="style22">', $aR[10]);
                $bG = array();
                foreach ($bR as $k => $b) {
                    if ($k != 0) {
                        $g = explode('</b>', str_replace('<b>', '', $b));
                        $bG [] = (float)$g[0];
                    }
                }
                
                echo $student->citizen_id.' '.$student->firstname . ' ' . $student->lastname . ' (' . $student->room . ') Response filtered!<br />';
                try {
                    $data = [
                        'thai' => $aG[0],
                        'social' => $aG[1],
                        'english' => $aG[2],
                        'math' => $aG[3],
                        'science' => $aG[4],
                        'apt' => $bG[2],
                        'sum' => $bG[3]
                    ];
                } catch (\Exception $e) {
                    $data = array();
                    echo '<h3 style="color:red">INVALID</h3>';
                }
                if (!empty($data)) {
                    \DB::table('temp_m6')->where('citizen_id', $student->citizen_id)->update($data);
                }
                dump($aG);
                dump($bG);
                echo "<br /><br />\n";
            } else {
                if (str_contains($response, 'ใส่ตัวเลขติดกัน 13 หลัก')) {
                    \DB::table('temp_m6')->where('citizen_id', $student->citizen_id)->update(['response' => 'NO']);
                } elseif (str_contains($response, '----')) {
                    echo $student->citizen_id . ' ' . $student->firstname . ' ' . $student->lastname . ' (' . $student->room . ') DIDN\'T TAKE TEST!<br />';
                } else {
                    echo $student->citizen_id . ' ' . $student->firstname . ' ' . $student->lastname . ' (' . $student->room . ') Response ERROR!<br />';
                }
            }
        }
    }
    
    public function logout(Request $request) {
        $request->session()->flush();
        
        return redirect('/');
    }
}
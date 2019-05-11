<?php

namespace App;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Request;

/**
 * Helper functions
 * Copyright (c) 2017 Siwat Techavoranant
 */
class Helper {
    
    const Round_Waiting = 'WAITING';
    const Round_Confirm = 'CONFIRM';
    const Round_Register = 'REGISTER';
    const Round_Audition = 'AUDITION';
    const Round_Glean = 'GLEAN';
    const Round_Closed = 'CLOSED';
    
    /**
     * Is currently in supplied round?
     *
     * @param string $round
     * @return bool
     */
    public static function isRound(string $round, bool $ignoreCountdown = false) {
        if (self::shouldCountdown() AND !$ignoreCountdown) {
            // Force round WAITING
            return $round == self::Round_Waiting;
        }
        
        return in_array(strtoupper($round), explode('&', Setting::getValue('round')));
    }
    
    public static function shouldCountdown(): bool {
        $registerTime = Setting::getValue('allow_register_time');
        
        return !empty($registerTime) AND $registerTime > time();
    }
    
    
    /**
     * Generate html <option> from array.
     *
     * @param array       $options
     * @param string|NULL $selected
     * @return string
     */
    public static function createOption(array $options, string $selected = NULL): string {
        // Input: ['value'] or ['option' => 'value']
        $html = '';
        $valueDifferent = self::isMultiDimensionalArray($options);
        foreach ($options as $ov => $option) {
            $html .= '<option value="' . $option . '"' . ($option == $selected ? ' selected' : '') . '>' . ($valueDifferent ? $ov : $option) . '</option>';
        }
        
        return $html;
    }
    
    public static function isArrayNotEmpty($input, bool $strict = false): bool {
        if (!empty($input) && is_array($input) && count(array_filter($input)) > 0) {
            $valueFound = false;
            $emptyFound = false;
            foreach ($input as $r1) {
                if (!empty($r1)) {
                    if (is_object($r1)) {
                        $r1 = json_decode(json_encode($r1), true);
                    }
                    if (is_array($r1)) {
                        foreach ($r1 as $r2) {
                            if (!empty($r2)) {
                                $valueFound = true;
                            } elseif ($strict) {
                                $emptyFound = true;
                                break;
                            }
                        }
                    } else {
                        $valueFound = true;
                    }
                } elseif ($strict) {
                    $emptyFound = true;
                    break;
                }
            }
            
            return $strict ? (!$emptyFound && $valueFound) : $valueFound;
        }
        
        return false;
    }
    
    /**
     * Roughly check if array is multidimensional (['example'=>'ตัวอย่าง']) array or not (['1','4']).
     *
     * @param array $input
     * @return bool
     */
    public static function isMultiDimensionalArray(array $input) {
        foreach ($input as $key => $val) {
            if (!is_numeric($key)) {
                return true;
            }
        }
        
        return false;
    }
    
    public static function stringToThaiDate($input = NULL) {
        if (empty($input)) {
            return '';
        }
        $time = strtotime($input);
        
        return date('j', $time) . ' ' . config('static.months')[date('n', $time)] . ' ' . (date('Y', $time) + 543);
    }
    
    public static function dateToAcademicYear($date) {
        $time = strtotime($date);
        if (is_numeric($date) && (strlen($date) == 10)) {
            // Input is unix timestamp
            $time = $date;
        }
        // Begin new academic year from mid-April
        $ce = date('o', $time) + 543;
        if (date('W', $time) <= 15) {
            $ce--;
        }
        
        return $ce;
    }
    
    public static function objectToArray($object) {
        return json_decode(json_encode($object), true);
    }
    
    /**
     * Get the IP address from request, use X-Real-IP if available.
     *
     * @param Request $request
     * @return string IP address
     */
    public static function getIPAddress(Request $request) {
        if ($request->ip() == '10.100.101.7' && isset($_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } else {
            $ip = $request->ip();
        }
        
        return $ip;
    }
    
}

<?php

namespace App;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Request;

/**
 * Helper functions
 */
class Helper {
	/**
	 * Generate html <option> from array.
	 *
	 * @param array       $options
	 * @param string|NULL $selected
	 * @return string
	 */
	public static function createOption(array $options, string $selected = NULL):string {
		// Input: ['value'] or ['option' => 'value']
		$html = '';
		$valueDifferent = self::isMultiDimensionalArray($options);
		foreach ($options as $ov => $option) {
			$html .= '<option value="' . $option . '"' . ($option == $selected ? ' selected' : '') . '>' . ($valueDifferent ? $ov : $option) . '</option>';
		}
		
		return $html;
	}
	
	public static function isArrayNotEmpty($input, bool $strict = false):bool {
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
	
	public static function getFileFromURI(string $uriencoded) {
		return base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $uriencoded));
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
	
	public static function generateCheck(bool $status) {
		return $status ? '<i class="material-icons green-text" title="มีข้อมูลเรียบร้อยแล้ว">check</i>' : '<i class="material-icons red-text" title="ไม่มีข้อมูลหรือไม่ครบถ้วน">clear</i>';
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
	 * Check if the given request came from inside the school's network
	 *
	 * Check if the given request came from 172.17.x.x (Ethernet), 192.168.x.x (Computer Lab), 10.100.100.x. (Some Server), 10.0.x.x (Wifi)
	 * 10.100.101.x is not permitted, as misconfiguration may cause REMOTE_ADDR to be its own ip.
	 * This method also return true if running in local environment and the request came from ::1 (localhost).
	 *
	 * @param Request $request
	 * @param bool    $checkSession
	 * @return bool
	 */
	public static function isIntranet(Request $request, bool $checkSession = true) {
		$ip = self::getIPAddress($request);
		
		return starts_with($ip, '172.17.') || starts_with($ip, '192.168.') || starts_with($ip, '10.100.100.') || starts_with($ip,
			'10.0.') || ($request->session()->has('local') && $checkSession) || ($ip == '::1' && config('app.env') == 'local');
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
	
	/**
	 * Compress the given object, json encode if not string.
	 *
	 * @param $input String, array, integer, float, collection and model are allowed.
	 * @return string
	 */
	public static function compress($input) {
		if ($input instanceof Jsonable) {
			$input = $input->toJson();
		} elseif ($input instanceof Arrayable) {
			$input = json_encode($input->toArray());
		} elseif ($input instanceof \JsonSerializable || is_array($input) || is_bool($input)) {
			$input = json_encode($input);
		}
		
		return base64_encode(gzcompress($input));
	}
}

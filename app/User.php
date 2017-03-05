<?php

namespace App;

use App\Exceptions\UserFriendlyException;
use Illuminate\Database\Eloquent\Model;
use Log;

/**
 * App\User
 *
 * @property int $citizen_id
 * @property float $student_id
 * @property bool $level
 * @property string $room
 * @property bool $number
 * @property string $title
 * @property string $firstname
 * @property string $lastname
 * @property string $club_id
 * @property string $reason
 * @property string $comment
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCitizenId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereClub($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereComment($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereFirstname($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereLastname($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereLevel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereReason($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereRoom($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereStudentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereTitle($value)
 * @mixin \Eloquent
 * @property-read \App\Club $club
 * @method static \Illuminate\Database\Query\Builder|\App\User whereClubId($value)
 */
class User extends Model {
    
    protected $primaryKey = 'citizen_id';
    
    const RegisterType_ExistingMember = 'EXISTING';
    const RegisterType_Audition = 'AUDITION';
    const RegisterType_War = 'WAR';
    const RegisterType_Special = 'SPECIAL';
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'citizen_id'
    ];
    
    /**
     * Define many-to-one relationship
     *
     * @return mixed
     */
    public function club() {
        return $this->belongsTo('App\Club', 'club_id');
    }
    
    /**
     * Get currently login user
     *
     * @return User
     * @throws \Exception
     */
    public static function current() {
        if ($user = self::find(session('student'))) {
            return $user;
        } else {
            $e = new UserFriendlyException('User not logged in');
            $e->setDescription('นักเรียนยังไม่ได้เข้าสู่ระบบ');
            throw $e;
        }
    }
    
    /**
     * Register the student to club
     *
     * @param string $clubId
     * @param string $registerType
     * @return bool
     */
    public function registerClub(string $clubId, string $registerType):bool {
        if ($club = Club::find($clubId) AND $club->isAvailable() AND !$this->hasClub()) {
            $this->club_id = $club->id;
            $this->reason = $registerType;
            if ($this->save()) {
                Log::info('Student '.$this->citizen_id.' has registered for club '.$club->id);
                return true;
            } else {
                Log::error('Student '.$this->citizen_id.' attempted to register for club '.$club->id);
            }
        }
        return false;
    }
    
    /**
     * Get this student's club in the previous year
     * return false if not found.
     */
    public function getPreviousClub() {
        if ($old = \DB::table('old_users')->where('citizen_id', $this->citizen_id)->first()) {
            if (!empty($old->club_id)) {
                return $old->club_id;
            }
        }
        return false;
    }
    
    /**
     * Has club?
     *
     * @return bool
     */
    public function hasClub() {
        return !empty($this->club_id);
    }
}

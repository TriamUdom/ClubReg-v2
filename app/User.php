<?php

namespace App;

use App\Exceptions\UserFriendlyException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Log;

/**
 * App\User
 *
 * Copyright (c) 2017 Siwat Techavoranant
 *
 * @property string                                                        $password
 * @property float                                                         $student_id
 * @property bool                                                          $level
 * @property string                                                        $room
 * @property bool                                                          $number
 * @property string                                                        $title
 * @property string                                                        $firstname
 * @property string                                                        $lastname
 * @property string                                                        $club_id
 * @property string                                                        $reason
 * @property string                                                        $comment
 * @property string                                                        $old_club_id
 * @property bool                                                          $confirmed
 * @method static Builder|User whereCitizenId($value)
 * @method static Builder|User whereClub($value)
 * @method static Builder|User whereComment($value)
 * @method static Builder|User whereFirstname($value)
 * @method static Builder|User whereLastname($value)
 * @method static Builder|User whereLevel($value)
 * @method static Builder|User whereNumber($value)
 * @method static Builder|User whereReason($value)
 * @method static Builder|User whereRoom($value)
 * @method static Builder|User whereStudentId($value)
 * @method static Builder|User whereTitle($value)
 * @mixin \Eloquent
 * @property-read Club                                                $club
 * @method static Builder|User whereClubId($value)
 * @property \Carbon\Carbon                                                $created_at
 * @property \Carbon\Carbon                                                $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Audition[] $auditions
 */
class User extends Model {
    const RegisterType_ExistingMember = 'EXISTING';
    const RegisterType_Audition = 'AUDITION';
    const RegisterType_War = 'WAR';
    const RegisterType_Special = 'SPECIAL';
    protected $primaryKey = 'student_id';
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = array(
        'password'
    );
    
    /**
     * Get currently login user
     *
     * @return User
     * @throws \Exception
     */
    public static function current() {
        if ($user = self::find(session('userid'))) {
            return $user;
        } else {
            $e = new UserFriendlyException('User not logged in');
            $e->setDescription('นักเรียนยังไม่ได้เข้าสู่ระบบ');
            throw $e;
        }
    }
    
    /**
     * Define many-to-one relationship
     *
     * @return mixed
     */
    public function club() {
        return $this->belongsTo('App\Club', 'club_id');
    }
    
    /**
     * Get the audition records of this user.
     */
    public function auditions() {
        return $this->hasMany('App\Audition', 'student_id', 'student_id');
    }

    public function getAuditions() {
        $auditions = array();

        foreach (\App\Audition::all() as $audition) {
            if (str_pad($this->student_id, 5, "0", STR_PAD_LEFT) ==
                str_pad($audition->student_id, 5, "0", STR_PAD_LEFT)) {
                $auditions[] = $audition;
            }
        }

        return $auditions;
    }
    
    /**
     * Register the student to club
     *
     * @param string $clubId
     * @param string $registerType
     * @return bool
     */
    public function registerClub(string $clubId, string $registerType): bool {
        if ($club = Club::find($clubId) and $club->isAvailableForLevel($this->level) and !$this->hasClub()) {
            $this->club_id = $club->id;
            $this->reason = $registerType;
            if ($this->save()) {
                Log::info('Student ' . $this->student_id . ' has registered for club ' . $club->id);
                
                return true;
            } else {
                Log::error('Student ' . $this->student_id . ' attempted to register for club ' . $club->id);
            }
        }
        
        return false;
    }
    
    /**
     * Get this student's club in the previous year
     * return false if not found.
     * @param bool $asModel
     * @return bool|Club|string
     */
    public function getPreviousClub(bool $asModel = false) {
        if (strlen($this->old_club_id) != 0) {
            if ($asModel) {
                return Club::find($this->old_club_id);
            } else {
                return $this->old_club_id;
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
    
    public function getName() {
        return $this->title . $this->firstname . ' ' . $this->lastname;
    }
    
    /**
     * Get primary key value
     *
     * @return string|int
     */
    public function getId() {
        return $this->{$this->primaryKey};
    }
}

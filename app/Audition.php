<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Audition
 * Copyright (c) 2017 Siwat Techavoranant
 *
 * @property int            $id
 * @property float          $citizen_id
 * @property string         $club_id
 * @property string         $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|Audition whereCitizenId($value)
 * @method static \Illuminate\Database\Query\Builder|Audition whereClub($value)
 * @method static \Illuminate\Database\Query\Builder|Audition whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Audition whereId($value)
 * @method static \Illuminate\Database\Query\Builder|Audition whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|Audition whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Club $club
 * @method static \Illuminate\Database\Query\Builder|Audition whereClubId($value)
 * @property string         $comment
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Query\Builder|Audition whereComment($value)
 */
class Audition extends Model {
    
    const Status_Awaiting = 'AWAITING'; // Beginning status (Step 1)
    const Status_Failed = 'FAILED'; // Audition failed
    const Status_Canceled = 'CANCELED'; // User cancel the audition request (in step 1)
    const Status_Passed = 'PASSED'; // Student is permited to join the club, but has not confirmed. (Step 2)
    const Status_Rejected = 'REJECTED'; // Student rejected
    const Status_Joined = 'JOINED'; // Student joined the club (Step 3)
    
    protected $fillable = ['citizen_id', 'club_id'];
    
    /**
     * Apply for club audition
     *
     * @param $citizenId
     * @param $clubId
     * @return Audition|bool
     */
    public static function apply($citizenId, $clubId) {
        if (self::findRequest($citizenId, $clubId)) {
            // Already exist
            return false;
        }
        $audition = new self (['citizen_id' => $citizenId, 'club_id' => $clubId]);
        $audition->status = self::Status_Awaiting;
        $audition->save();
        
        return $audition;
    }
    
    /**
     * Find audition request by citizen id and club id
     *
     * @param $citizenId
     * @param $clubId
     * @return bool|Audition|null|static
     */
    public static function findRequest($citizenId, $clubId) {
        return self::where('citizen_id', $citizenId)->where('club_id', $clubId)->first() ?? false;
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
     * Define many-to-one relationship
     *
     * @return mixed
     */
    public function user() {
        return $this->belongsTo('App\User', 'citizen_id');
    }
    
    /**
     * Update audition request status
     *
     * @param string $status
     */
    public function updateStatus(string $status) {
        $this->status = $status;
        $this->save();
    }
    
    public function getStatus() {
        switch ($this->status) {
            case self::Status_Awaiting:
                return 'รอออดิชั่น/การตอบรับจากชมรม';
            case self::Status_Failed:
                return 'ถูกชมรมปฏิเสธ';
            case self::Status_Joined:
                return 'เข้าร่วมชมรมแล้ว';
            case self::Status_Passed:
                return 'ผ่านการคัดเลือก โปรดยืนยันหรือปฏิเสธ';
            case self::Status_Rejected:
                return 'ปฏิเสธการเข้าชมรม';
            default:
                return $this->status;
        }
    }
}
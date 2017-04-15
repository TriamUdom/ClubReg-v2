<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

/**
 * Database-based Setting
 * Values will be saved as JSON to maintain variable type.
 *
 * @package App
 * @property string                                        $id
 * @property string|int|float|\stdClass|array|boolean|null $value
 * @property \Carbon\Carbon                                $created_at
 * @property \Carbon\Carbon                                $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Setting whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Setting whereValue($value)
 * @mixin \Eloquent
 */
/*
 * Defined setting:
 *  - allow_register_time: Timestamp to start registering club, 0 for no countdown, overriding "round" value. (used to show countdown)
 *  - round: Enumerated types: WAITING, CONFIRM, CONFIRM&AUDITION, AUDITION, WAR, CLOSED (must be uppercase)
 */
class Setting extends Model {
    public $incrementing = false;
    
    /**
     * Get the value
     *
     * @param  string $value
     * @return string|int|float|\stdClass|array|boolean|null
     */
    public function getValueAttribute($value) {
        return json_decode($value);
    }
    
    /**
     * Set the value
     *
     * @param  string $value
     * @return void
     */
    public function setValueAttribute($value) {
        $this->attributes['value'] = json_encode($value);
    }
    
    public static function getValue(string $id) {
        return self::find($id)->value;
    }
}
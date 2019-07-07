<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Problem
 *
 *  @property string $real_club_id
 */
class Problem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invalid';
    protected $primaryKey = 'student_id';
    public $timestamps = false;
}

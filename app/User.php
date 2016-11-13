<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'citizen_id'
    ];
    
    public function club() {
        return $this->belongsTo('App\Club', 'club');
    }
}

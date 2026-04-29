<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitPeople extends Model
{
    protected $hidden = [
        'id_unit'
    ];
    
    public $timestamps = false;

    public $table = 'unit_peoples';
}

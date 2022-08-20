<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Absences extends Model
{
    protected $table = 'absences';

    public function scopeJoinUsers($query)
		{
			return $query->leftjoin('users', 'users.id', '=', 'absences.id_user');
		}		
}

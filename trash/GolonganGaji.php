<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

	class GolonganGaji extends Model
	{
		protected $table = 'golongan_gaji';

		public function scopeJoinJabatan($query)
	    {
	        return $query->join('jabatan', 'jabatan.id', '=', 'golongan_gaji.jabatan');
	    }
	}
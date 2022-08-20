<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

	class Karyawan extends Model
	{
		protected $table = 'karyawan';

		public function scopeJoinCabang($query)
	    {
	        return $query->join('cabang', 'cabang.id', '=', 'karyawan.cabang');
	    }

		public function scopeJoinJabatan($query)
	    {
	        return $query->join('jabatan', 'jabatan.id', '=', 'golongan_gaji.jabatan');
	    }

		public function scopeJoinGolonganGaji($query)
	    {
	        return $query->join('golongan_gaji', 'golongan_gaji.id', '=', 'karyawan.golongan_gaji');
	    }
	}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stationary_transaction extends Model
{
     protected $table = 'stationary_transaction';

      public function scopeJoinstationery_stock($query)
		{
			return $query->leftjoin('stationery_stock', 'stationery_stock.kode_barang', '=', 'stationary_transaction.kode_barang');
		}
	 public function scopeJoinStationary_Kategory($query)
		{
			return $query->leftjoin('stationary_kategory', 'stationary_kategory.unik_kategory', '=', 'stationary_transaction.kode_barang');
		}
}

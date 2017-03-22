<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'street','city','postcode'
	];

	public function profileDelivery() {
		return $this->belongsTo('App\UserProfile','delivery_id');
	}
	public function profileBilling() {
		return $this->belongsTo('App\UserProfile','billing_id');
	}


}

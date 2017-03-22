<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $primaryKey = "user_id";
	protected $fillable = [
	  'telephone'
	];

	public function billing() {
		return $this->hasOne(Address::class,'id','billing_id');
	}
	public function delivery() {
		return $this->hasOne(Address::class,'id','delivery_id');
	}

	public function setDelivery(int $id) {
		$this->delivery_id = $id;
		return $this;
	}
	public function setBilling(int $id) {
		$this->billing_id = $id;
		return $this;
	}



}

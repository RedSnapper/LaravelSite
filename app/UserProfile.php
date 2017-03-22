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

	public function user() {
		return $this->belongsTo(User::class);
	}

	public function delivery() {
		return $this->belongsTo(Address::class,'delivery_id');
	}

	public function billing() {
		return $this->belongsTo(Address::class,'billing_id');
	}


}

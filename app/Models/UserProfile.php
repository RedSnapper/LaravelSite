<?php

namespace App\Models;

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
	  'telephone','delivery_id',
	];

	public function user() {
		return $this->belongsTo(User::class);
	}

	public function delivery() { //This field belongsTo that table...
		return $this->belongsTo(Address::class,'delivery_id');
	}

	public function billing() { //This field belongsTo that table...
		return $this->belongsTo(Address::class,'billing_id');
	}


}

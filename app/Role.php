<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	protected $fillable = [
		'name'
	];

	/**
	 * many-many relations
	 **/

	//public function users() {
	//	return $this->belongsToMany(User::class);
	//}

	public function mm() {
		return $this->belongsToMany(User::class);
	}

}

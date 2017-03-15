<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	protected $fillable = [
		'name'
	];

	/**
	 * Users for a Role.
	 **/
	public function users() {
		return $this->belongsToMany(User::class);
	}

}

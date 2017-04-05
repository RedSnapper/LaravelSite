<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model {

	public function roles() {
		return $this->belongsToMany(Role::class);
	}

}

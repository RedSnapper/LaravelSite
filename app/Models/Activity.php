<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model {

	protected $fillable = [
		'name','label','category_id','comment'
	];

	public function roles() {
		return $this->belongsToMany(Role::class);
	}

	/**
	 * The following is commented because it may be plausible for material roles to be available to teams.
	 */
	public function availableRoles() {
		return $this->belongsToMany(Role::class)->getRelated()->get();
//		return $this->belongsToMany(Role::class)->getRelated()->unteamed()->get();
	}

	public function category() {
		return $this->belongsTo(Category::class);
	}

}

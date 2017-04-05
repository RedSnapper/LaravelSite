<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	protected $fillable = [
		'name','category_id'
	];

	public function category() {
		return $this->belongsTo(Category::class);
	}

	public function mm() {
		return $this->belongsToMany(User::class);
	}

	public function users() {
		return $this->belongsToMany(User::class);
	}

	public function activities() {
		return $this->belongsToMany(Activity::class);
	}

	public function givePermissionTo(Activity $activity){
		return $this->activities()->save($activity);
	}

}

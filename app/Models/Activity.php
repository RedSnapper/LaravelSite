<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model {
	protected $fillable = [
		'name','label','category_id'
	];

	protected $fillable = [
	  'name','label'
	];

	public function roles() {
		return $this->belongsToMany(Role::class);
	}

	public function category() {
		return $this->belongsTo(Category::class);
	}

}

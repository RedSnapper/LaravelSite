<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	protected $fillable = [
		'name','category_id'
	];


	public function category() {
		return $this->hasOne(Category::class);
	}

	public function mm() {
		return $this->belongsToMany(User::class);
	}

}

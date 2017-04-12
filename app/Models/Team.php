<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model {
	protected $fillable = [
		'name',
		'category_id'
	];

	public function category() {
		return $this->belongsTo(Category::class);
	}

	public function users() {
		return $this->belongsToMany(User::class,'role_team_user','team_id','user_id')->withPivot('role_id');
	}

	public function roles() {
		return $this->belongsToMany(Role::class,'role_team_user','team_id','role_id')->withPivot('user_id');
	}


}
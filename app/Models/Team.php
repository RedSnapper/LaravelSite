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

	public function users(int $role = null) {
		return $this->belongsToMany(User::class,'role_team_user','user_id','team_id')->withPivot('role_id',$role);
	}

	public function roles(int $user = null) {
		return $this->belongsToMany(Role::class,'role_team_user','role_id','team_id')->withPivot('user_id',$user);
	}


}
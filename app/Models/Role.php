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
		return $this->users($team);
	}


	public function activities() {
		return $this->belongsToMany(Activity::class);
	}

	public function users() {
		return $this->belongsToMany(User::class);
	}


	public function teamUsers(int $team) {
		return $this->belongsToMany(User::class, 'role_team_user', 'role_id', 'user_id')->wherePivot('team_id','=',$team);
	}

	public function userTeams(int $user) {
		return $this->belongsToMany(Team::class, 'role_team_user', 'role_id', 'team_id')->wherePivot('user_id','=',$user);
	}

	public function categories() {
		return $this->belongsToMany(Category::class);
	}

	public function givePermissionTo(Activity $activity){
		return $this->activities()->save($activity);
	}

	public function givePermissionToCategory(Category $category){
		return $this->categories()->save($category);
	}

	public static function options() {
		return with(new static)->all()->pluck('name','id');
	}


}

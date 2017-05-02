<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class Team extends Model {
	protected $fillable = [
		'name',
		'category_id'
	];


	public function category() {
		return $this->belongsTo(Category::class);
	}

	public function attachRoleUsers(int $role,array $users) {
		$this->roleUsers()->attach($users,['role_id'=>$role]);
	}

	public function syncRoleUsers(int $role,array $users) {
		$sync=[];
		foreach($users as $user) {
			$sync[$role] = ['user_id'=>$user];
		}
		$this->userRoles()->wherePivot('user_id',$user)->sync($sync);
	}


	public function attachUserRoles(int $user,array $roles) {
		$this->userRoles()->attach($roles,['user_id'=>$user]);
	}

	public function syncUserRoles(int $user,array $roles) {
		$sync=[];
		foreach($roles as $role) {
			$sync[$role] = ['user_id'=>$user];
		}
		$this->userRoles()->wherePivot('user_id',$user)->sync($sync);
	}


	public function roleUsers() {
		return $this->belongsToMany(User::class,'role_team_user','team_id','user_id')->withPivot('role_id');
	}

	public function userRoles() {
		return $this->belongsToMany(Role::class,'role_team_user','team_id','role_id')->withPivot('user_id');
	}

	/**
	 * TODO: Visibility of this list should probably be set by either/both:
	 * 1: the visibility (by role) of the categories it is in.
	 * 2: being a member of the team.
	 **/
	public static function options(Collection $categories = null) {
		return is_null($categories) ?
		 with(new static)->pluck('name','id')
		:
		 with(new static)->whereIn('category_id',$categories)->pluck('name','id');
	}

	public static function getIds(Collection $categories = null) {
		return is_null($categories) ?
			with(new static)->pluck('id')
			:
			with(new static)->whereIn('category_id',$categories)->pluck('id');
	}


}
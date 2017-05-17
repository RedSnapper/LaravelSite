<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
	use Notifiable;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'email',
		'password',
	];
	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * Set the password
	 *
	 * @param  string $value
	 * @return void
	 */
	public function setPasswordAttribute($value) {
		$this->attributes['password'] = bcrypt($value);
	}

	public function profile() {
		return $this->hasOne(UserProfile::class);
	}

	public function roles() {
		return $this->belongsToMany(Role::class);
	}

	public function syncTeamRoles(Team $team, array $roles) {
		$teamId = $team->getKey();
		$sync = [];
		foreach ($roles as $role) {
			$sync[$role] = ['team_id' => $teamId];
		}
		$this->teamRoles()->wherePivot('team_id', $teamId)->sync($sync);
	}

	public function teamRoles() {
		return $this->belongsToMany(Role::class, 'role_team_user', 'user_id', 'role_id')->withPivot('team_id');
	}

	public function roleTeams() {
		return $this->belongsToMany(Team::class, 'role_team_user', 'user_id', 'team_id')->withPivot('role_id');
	}

	public function teams() {
		return $this->belongsToMany(Team::class, 'role_team_user', 'user_id', 'team_id')->groupBy([
			"teams.id",
			"role_team_user.user_id"
		]);
	}

	public function hasRole($role, $team = null) {
		if (is_null($team)) {
			if (is_string($role)) {
				return $this->roles->contains('name', $role);
			}
			return !!$role->intersect($this->roles)->count();
		} else {
			if (is_string($role)) {
				return $this->teamRoles->wherePivot('team_id', $team)->contains('name', $role);
			}
			return !!$role->intersect($this->teamRoles->wherePivot('team_id', $team))->count();
		}
	}

	public function assignRole($role, $team = null) {
		if (is_null($team)) {
			return $this->roles()->save(
				Role::whereName($role)->firstOrFail()
			);
		}
	}
}

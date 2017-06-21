<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

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
	public function setPasswordAttribute($value) : void {
		$this->attributes['password'] = bcrypt($value);
	}

	public function profile() : HasOne  {
		return $this->hasOne(UserProfile::class);
	}

	public function roles() : BelongsToMany {
		$result = $this->belongsToMany(Role::class);
		$result->getQuery()->where("roles.team_based",false);
		return $result;
	}


	public function teamRoles() : BelongsToMany {
		$result = $this->belongsToMany(Team::class, 'role_team_user','user_id','team_id')->withPivot('role_id');
		return $result;
	}

	public function roleTeams() : BelongsToMany {
		$result = $this->belongsToMany(Role::class,'role_team_user','user_id','role_id')->withPivot('team_id');
		$result->getQuery()->where("roles.team_based",true);
		return $result;
	}

	public function teams() : BelongsToMany {
		return $this->belongsToMany(Team::class, 'role_team_user', 'user_id', 'team_id')->groupBy([
			"teams.id",
			"role_team_user.user_id"
		]);
	}


	public function hasRole($role, $team = null) : bool {
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

//public function revoke
	public function assignRole($role, $team = null) {
		if (is_null($team)) {
			return $this->roles()->save(
				Role::whereName($role)->firstOrFail()
			);
		}
	}
}

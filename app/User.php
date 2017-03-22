<?php

namespace App;

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

	/**
	 * many-many relations Roles for a User.
	 **/
	public function mm() {
		return $this->belongsToMany(Role::class);
	}

	public function hasRole($role) {
		if (is_string($role)) {
			return $this->roles->contains('name', $role);
		}
		return !!$role->intersect($this->roles)->count();
	}

	public function assignRole($role) {
		return $this->roles()->save(
			Role::whereName($role)->firstOrFail()
		);
	}

}

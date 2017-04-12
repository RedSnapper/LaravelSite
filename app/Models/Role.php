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

	public function mm(int $team=null) {
		return $this->users($team);
	}


	public function activities() {
		return $this->belongsToMany(Activity::class);
	}

	public function users(int $team=0) {
		return $this->belongsToMany(User::class,'role_team_user','user_id','role_id')->withPivot('team_id',$team);
	}

	public function teams(int $user=null) {
		return $this->belongsToMany(Team::class,'role_team_user','team_id','role_id')->withPivot('user_id',$user);
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

}

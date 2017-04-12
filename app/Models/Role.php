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

	//public function teams() {
	//	return $this->belongsToMany(Team::class,'role_team_user','role_id','team_id')
	//}

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

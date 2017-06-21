<?php

namespace App\Models;

use App\Policies\Helpers\UserPolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;


class Role extends Model
{
	protected $fillable = [
		'name','category_id','team_based'
	];
	protected $casts = [
		'team_based' => 'integer'
	];


	public function scopeTeamed(Builder $query) {
		return $query->where('team_based',true);
	}

	public function scopeUnteamed(Builder $query) {
		return $query->where('team_based',false);
	}

	public function category() {
		return $this->belongsTo(Category::class);
	}

	public function activities() {
		return $this->belongsToMany(Activity::class);
	}

	public function users() {
		return $this->unteamed->belongsToMany(User::class);
	}

	public function teamUsers(int $team) {
		return $this->teamed->belongsToMany(User::class, 'role_team_user', 'role_id', 'user_id')->wherePivot('team_id','=',$team);
	}

	public function userTeams(int $user) {
		return $this->teamed->belongsToMany(Team::class, 'role_team_user', 'role_id', 'team_id')->wherePivot('user_id','=',$user);
	}

	public function categories() {
		$result = $this->belongsToMany(Category::class)->withPivot('modify');
		$result->getQuery()->ordered();
		return $result;
	}

	public function givePermissionTo(Activity $activity){
		return $this->activities()->save($activity);
	}

	public function allowUser(User $user){
		return $this->unteamed->users()->save($user);
	}

	public function allowTeamUser(Team $team,User $user){
		return $this->teamed->teamUsers($team->id)->save($user,['team_id'=>$team->id]);
	}

	public function givePermissionToCategory(Category $category,int $modify = UserPolicy::CAN_ACCESS){
		return $this->categories()->save($category,['modify'=>$modify]);
	}

	public static function options(Collection $categories = null,bool $forTeams = true) {
		return with(new static)->whereIn('category_id',$categories)->where('team_based','=',$forTeams)->pluck('name','id');
	}


}

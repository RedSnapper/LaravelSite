<?php
namespace App\Policies\Helpers;

use App\Models\Activity;
use App\Models\Category;
use App\Models\User;
use App\Models\Team;

use Illuminate\Database\Connection;
use Illuminate\Support\Collection;


/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 18/04/2017 14:12
 */
class PolicyData {

	private $connection;

	private $categories;
	private $users;
	private $teams;
	private $teamCats;


	public function __construct(Connection $connection) {
		$this->teams = collect();
		$this->teamCats = collect();
		$this->users = collect();
		$this->categories = collect();
		$this->connection = $connection;
	}

	public function isCategoryAvailable(User $user, Category $category){
		if(!$this->categories->has($user->id)) {
			$this->categories->put($user->id,$this->getAvailableCategories($user->id));
		}
		return $this->categories->get($user->id)->contains($category->id);
	}


	public function hasUserTeamActivity(User $user, Team $team,string $activity = null) : bool {
		if (!$this->teams->has($user->id)) {
			$this->teams->put($user->id, $this->getAvailableTeams($user->id));
		}
		$userTeams = $this->teams->get($user->id);
		if($userTeams->has($team->id)) {
			$userTeam = $userTeams->get($team->id);
			return is_null($activity) || $userTeam->contains('activity',$activity);
		}
		return false;
	}

	public function hasUserTeamCategory(User $user,int $team,int $category) : bool {
		if (!$this->teamCats->has($user->id)) {
			$this->teamCats->put($user->id,$this->getAvailableTeamCategories($this->connection,$user->id));
		}
		$teamCats = $this->teamCats->get($user->id);
		if($teamCats->has($team)) {
			$teamCat = $teamCats->get($team);
			return $teamCat->contains('category',$category);
		}
		return false;
	}

	private function getAvailableCategories(int $user) : Collection {
		$query = $this->connection->table('categories as self')->select('self.id')
			->join('categories', function ($join) {
				$join->on('self.idx', '<', 'categories.nextchild')->on('self.idx' ,'>=','categories.idx');
			})
			->join('category_role','categories.id','category_role.category_id')
			->join('role_user','category_role.role_id','role_user.role_id')
			->where('role_user.user_id',$user)
			->groupBy('self.id');

		return $query->pluck('id');
	}

	private static function getAvailableTeamCategories(Connection $connection,int $user) : Collection {
		$query = $connection->table('categories as self')->select(['self.id as category','role_team_user.team_id as team'])
			->join('categories', function ($join) {
				$join->on('self.idx', '<', 'categories.nextchild')->on('self.idx' ,'>=','categories.idx');
			})
			->join('category_role','categories.id','category_role.category_id')
			->join('role_team_user','category_role.role_id','role_team_user.role_id')
			->where('role_team_user.user_id',$user)
			->groupBy(['team','category']);
		return collect($query->get())->groupBy('team');
	}

	private function getAvailableTeams(int $user): Collection {
		$query = $this->connection->table('role_team_user as rtu')->select(['rtu.team_id as team','a.name as activity'])
			->join('activity_role as ar','ar.role_id','rtu.role_id')
			->join('activities as a','a.id','ar.activity_id')
			->where('rtu.user_id',$user);
		return collect($query->get())->groupBy('team');
	}



}
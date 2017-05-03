<?php

namespace App\Policies\Helpers;

use App\Models\Category;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserPolicy {
	private $connection;
	private $categories;
	private $teams;
	private $teamCategories;
	private static $userPolicy; //static reference to this.
	const INHERITING = 2;
	const CAN_ACCESS = 4;
	const CAN_MODIFY = 5;
	const NIL_ACCESS = 3;

	public function __construct(Connection $connection) {
		$this->teams = collect();
		$this->teamCategories = collect();
		$this->categories = collect();
		$this->connection = $connection;
		static::$userPolicy = $this;
	}

	static public function instance() {
		return static::$userPolicy;
	}

	/**
	 * Is the category available for this user
	 *
	 * @param User $user
	 * @param      $category
	 * @param int  $access one of CAN_ACCESS|CAN_MODIFY
	 * @return boolean
	 */
	public function hasCategory(User $user, $category, int $access) {
		$this->loadCategories($user);
		$category = $this->getCategoryID($category);

		$permission = $this->categories->get($user->id)->get($category);
		return is_null($permission) ? false : $permission->modify >= $access;
	}

	/**
	 * Does this user for this team have the associated activity
	 *
	 * @param User     $user
	 * @param Team|int $team
	 * @param string   $activity
	 * @return bool
	 */
	public function hasTeamActivity(User $user, $team, string $activity): bool {

		$team = $this->getTeamID($team);

		$this->loadTeams($user);

		$userPermissions = $this->teams->get($user->id);
		if(is_null($userPermissions)) return false;
		$userTeamPermissions = $userPermissions->get($team);
		if(is_null($userTeamPermissions)) return false;
		$permission = $userTeamPermissions->get($activity);
		return !is_null($permission);
	}

	/**
	 * @param User $user
	 * @param      $team
	 * @param      $category
	 * @param int  $access one of CAN_VIEW|CAN_MODIFY
	 * @return bool
	 */
	public function hasTeamCategory(User $user, $team, $category, int $access): bool {

		$category = $this->getCategoryID($category);
		$team = $this->getTeamID($team);

		$this->loadTeamCategories($user);

		$userPermissions = $this->teamCategories->get($user->id);
		if(is_null($userPermissions)) return false;

		$userTeamPermissions = $userPermissions->get($team);
		if(is_null($userTeamPermissions)) return false;

		$permission = $userTeamPermissions->get($category);
		return !is_null($permission);
		return ($permission->modify >= $access);


	}

	/**
	 * @param int|User $user
	 * @return Collection
	 */
	protected function getAvailableCategories(int $user): Collection {
		$user = $this->getUserID($user);
		$query = $this->connection->table('categories as self')->select('self.id', DB::raw('max(category_role.modify) as modify'))
			->join('categories', function ($join) {
				$join->on('self.idx', '<', 'categories.nextchild')->on('self.idx', '>=', 'categories.idx');
			})
			->join('category_role', 'categories.id', 'category_role.category_id')
			->join('role_user', 'category_role.role_id', 'role_user.role_id')
			->where('role_user.user_id', $user)
			->groupBy('self.id');
		return $query->get()->keyBy('id'); //->pluck('id,modify');
	}

	/**
	 * @param Connection $connection
	 * @param int|User   $user
	 * @return Collection
	 */
	protected function getAvailableTeamCategories($user): Collection {
		$user = $this->getUserID($user);
		$query = $this->connection->table('categories as self')->select(
			'self.id as category',
			'role_team_user.team_id as team',
			DB::raw('max(category_role.modify) as modify')
		)
			->join('categories', function ($join) {
				$join->on('self.idx', '<', 'categories.nextchild')->on('self.idx', '>=', 'categories.idx');
			})
			->join('category_role', 'categories.id', 'category_role.category_id')
			->join('role_team_user', 'category_role.role_id', 'role_team_user.role_id')
			->where('role_team_user.user_id', $user)
			->groupBy(['team', 'category']);
//		dd($query->toSql());
		$teams = $query->get()->groupBy('team');
		foreach ($teams as $id => $team) {
			$teams[$id] = $team->keyBy('category');
		}
		return $teams;
	}

	/**
	 * @param int|User $user
	 * @return Collection
	 */
	protected function getAvailableTeams($user): Collection {
		$userID = $this->getUserID($user);
		//The following deals with hard activities.
		$query = $this->connection->table('role_team_user as rtu')->select([
			'rtu.team_id as team',
			'a.name as activity'
		])
			->join('activity_role as ar', 'ar.role_id', 'rtu.role_id')
			->join('activities as a', 'a.id', 'ar.activity_id')
			->where('rtu.user_id', $userID);
		$teams = $query->get()->groupBy('team');

		$teamIds = $user->teams()->get()->keyBy('id');
		//now we need to get derived accesses...
		foreach (Category::sections()->get() as $section) {
			$category = $section->id;
			foreach ($teamIds as $id => $team) {
				$activity = $section->name . '_';
				$userTeamCat = $this->teamCategories->get($user->id)->get($id);
				if(!is_null($userTeamCat)) {

					//Section levels are complex. we want them to represent access only, but inheritance is a tricky bugger here.
					//if($userTeamCat->get($category) && $userTeamCat->get($category)->modify == UserPolicy::CAN_MODIFY ) {
					//	$accessItem = (object)['team'=>$id,'activity'=>$activity."ACCESS"];
					//	if(!$teams->has($id)) {
					//		$teams->put($id,collect());
					//	}
					//	$teams[$id]->push($accessItem);
					//}

					$accessItem = (object)['team'=>$id,'activity'=>$activity."ACCESS"];
					$modifyItem = (object)['team'=>$id,'activity'=>$activity."MODIFY"];
					if(!$teams->has($id)) {
						$teams->put($id,collect());
					}
					$teams[$id]->push($accessItem);
					if($userTeamCat->get($category) && $userTeamCat->get($category)->modify == UserPolicy::CAN_MODIFY ) {
						$teams[$id]->push($modifyItem);
					}
				}
			}
		}

		foreach ($teams as $id => $team) {
			$teams[$id] = $team->keyBy('activity');
		}
		return $teams;
	}

	/**
	 * @param $user
	 * @return int
	 */
	protected function getUserID($user): int {
		return is_a($user, User::class) ? $user->id : (int)$user;
	}

	/**
	 * @param $category
	 * @return int
	 */
	protected function getCategoryID($category): int {
		return is_a($category, Category::class) ? $category->id : (int)$category;
	}

	/**
	 * @param $team
	 * @return int
	 */
	protected function getTeamID($team): int {
		return is_a($team, Team::class) ? $team->id : (int)$team;
	}

	/**
	 * @param User $user
	 */
	protected function loadCategories(User $user) {
		if (!$this->categories->has($user->id)) {
			$this->categories->put($user->id, $this->getAvailableCategories($user->id));
		}
	}

	/**
	 * @param User $user
	 */
	protected function loadTeams(User $user) {
		$this->loadTeamCategories($user);
		if (!$this->teams->has($user->id)) {
			$this->teams->put($user->id, $this->getAvailableTeams($user));
		}
	}

	/**
	 * @param User $user
	 */
	protected function loadTeamCategories(User $user) {
		if (!$this->teamCategories->has($user->id)) {
			$this->teamCategories->put($user->id, $this->getAvailableTeamCategories($user->id));
		}
	}

}
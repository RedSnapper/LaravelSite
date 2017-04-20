<?php

namespace App\Policies\Helpers;

use App\Models\Category;
use App\Models\User;
use App\Models\Team;

use Illuminate\Database\Connection;
use Illuminate\Support\Collection;

class UserPolicy {

	private $connection;

	private $categories;
	private $teams;
	private $teamCategories;

	public function __construct(Connection $connection) {
		$this->teams = collect();
		$this->teamCategories = collect();
		$this->categories = collect();
		$this->connection = $connection;
	}

	/**
	 * Is the category available for this user
	 *
	 * @param User     $user
	 * @param Category $category
	 * @return mixed
	 */
	public function hasCategory(User $user, Category $category) {
		$this->loadCategories($user);
		return $this->categories->get($user->id)->contains($category->id);
	}

	/**
	 * Does this user for this team have the associated activity
	 *
	 * @param User   $user
	 * @param Team|int   $team
	 * @param string $activity
	 * @return bool
	 */
	public function hasTeamActivity(User $user, $team, string $activity): bool {

		$team = $this->getTeamID($team);

		$this->loadTeams($user);

		$userTeam = $this->teams->get($user->id)->get($team);

		if ($userTeam) {
			return $userTeam->contains('activity', $activity);
		}

		return false;
	}

	/**
	 * @param User $user
	 * @param      $team
	 * @param      $category
	 * @return bool
	 */
	public function hasTeamCategory(User $user, $team, $category): bool {

		$category = $this->getCategoryID($category);
		$team = $this->getTeamID($team);

		$this->loadTeamCategories($user);

		$teamCategory = $this->teamCategories->get($user->id)->get($team);

		if ($teamCategory) {
			return $teamCategory->contains('category', $category);
		}
		return false;
	}

	/**
	 * @param int|User $user
	 * @return Collection
	 */
	protected function getAvailableCategories(int $user): Collection {
		$user = $this->getUserID($user);
		$query = $this->connection->table('categories as self')->select('self.id')
		  ->join('categories', function ($join) {
			  $join->on('self.idx', '<', 'categories.nextchild')->on('self.idx', '>=', 'categories.idx');
		  })
		  ->join('category_role', 'categories.id', 'category_role.category_id')
		  ->join('role_user', 'category_role.role_id', 'role_user.role_id')
		  ->where('role_user.user_id', $user)
		  ->groupBy('self.id');

		return $query->pluck('id');
	}

	/**
	 * @param Connection $connection
	 * @param int|User        $user
	 * @return Collection
	 */
	protected function getAvailableTeamCategories($user): Collection {
		$user = $this->getUserID($user);
		$query = $this->connection->table('categories as self')->select([
		  'self.id as category',
		  'role_team_user.team_id as team'
		])
		  ->join('categories', function ($join) {
			  $join->on('self.idx', '<', 'categories.nextchild')->on('self.idx', '>=', 'categories.idx');
		  })
		  ->join('category_role', 'categories.id', 'category_role.category_id')
		  ->join('role_team_user', 'category_role.role_id', 'role_team_user.role_id')
		  ->where('role_team_user.user_id', $user)
		  ->groupBy(['team', 'category']);
		return collect($query->get())->groupBy('team');
	}

	/**
	 * @param int|User $user
	 * @return Collection
	 */
	protected function getAvailableTeams($user): Collection {
		$user = $this->getUserID($user);
		$query = $this->connection->table('role_team_user as rtu')->select([
		  'rtu.team_id as team',
		  'a.name as activity'
		])
		  ->join('activity_role as ar', 'ar.role_id', 'rtu.role_id')
		  ->join('activities as a', 'a.id', 'ar.activity_id')
		  ->where('rtu.user_id', $user);
		return collect($query->get())->groupBy('team');
	}
	/**
	 * @param $user
	 * @return int
	 */
	protected function getUserID($user): int {
		return is_a($user, User::class) ? $user->id : (int) $user;
	}

	/**
	 * @param $category
	 * @return int
	 */
	protected function getCategoryID($category): int {
		return is_a($category, Category::class) ? $category->id : (int) $category;
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
		if (!$this->teams->has($user->id)) {
			$this->teams->put($user->id, $this->getAvailableTeams($user->id));
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
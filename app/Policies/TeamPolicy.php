<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;

class TeamPolicy {
	use HandlesAuthorization;
	/**
	 * @var Collection
	 */
	private $teams;
	/**
	 * @var Connection
	 */
	private $connection;

	/**
	 * TeamPolicy constructor.
	 *
	 * @param Collection $teams
	 * @param Connection $connection
	 */
	public function __construct(Collection $teams, Connection $connection) {
		$this->teams = $teams;
		$this->connection = $connection;
	}

	protected function hasUserTeamActivity(User $user, Team $team,string $activity) : bool {
		if (!$this->teams->has($user->id)) {
			$this->teams->put($user->id, $this->getAvailableTeams($user->id));
		}
		if($this->teams->get($user->id)->has($team->id)) {
			$userTeam = $this->teams->get($user->id)->get($team->id);
			return $userTeam->contains('activity',$activity);
		}
		return false;
	}

	public function __call($activity, $arguments) {
		list($user,$team) = $arguments;
		return $this->hasUserTeamActivity($user,$team,$activity);
	}

	protected function getAvailableTeams(int $user): Collection {
		$query = $this->connection->table('role_team_user as rtu')->select(['rtu.team_id as team','a.name as activity'])
		  ->join('activity_role as ar','ar.role_id','rtu.role_id')
			->join('activities as a','a.id','ar.activity_id')
		  ->where('rtu.user_id',$user);
		return collect($query->get())->groupBy('team');
	}
}

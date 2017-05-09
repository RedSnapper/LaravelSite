<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\Team;
use App\Models\User;
use App\Policies\Helpers\UserPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class TeamPolicy
 *
 * @package App\Policies
 *
 * Team Membership determines team availability.
 * Team Categories do NOT determine team availability.
 * Access is determined by looking at the set of activities determined by
 * The team/roles for the user.
 *
 */
class TeamPolicy {
	use HandlesAuthorization;

	/**
	 * @var UserPolicy
	 */
	private $user;

	public function __construct(UserPolicy $user) {
		$this->user = $user;
	}

	//
	public function access(User $user, Team $team , Category $category  = null) {
		if (is_null($category)) {
			return $this->user->hasTeam($user, $team);
		} else {
			return $this->user->hasTeamCategory($user, $team, $category, UserPolicy::CAN_ACCESS);
		}
	}

	public function modify(User $user, Team $team , Category $category  = null) {
		if (is_null($category)) {
			return $this->user->hasTeam($user, $team);
		} else {
			return $this->user->hasTeamCategory($user, $team, $category, UserPolicy::CAN_MODIFY);
		}
	}

	public function __call($activity, $arguments) {

		list($user, $team, $category, $modify) = array_pad($arguments, 4, null);
		if(is_null($modify)) {
			$bits = explode('_',$activity);
			$modify = $bits[1] && $bits[1] === 'MODIFY' ? UserPolicy::CAN_MODIFY : UserPolicy::CAN_ACCESS;
		}

		if (is_null($category)) {
			return $this->user->hasTeam($user, $team, $activity);
		} else {
			return $this->user->hasTeamCategory($user, $team, $category, $modify ?? UserPolicy::CAN_ACCESS);
		}

	}

}

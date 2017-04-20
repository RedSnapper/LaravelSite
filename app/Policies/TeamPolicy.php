<?php

namespace App\Policies;

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

	public function __call($activity, $arguments) {

		list($user, $team, $category) = array_pad($arguments, 3, null);

		$hasTeamActivity = $this->user->hasTeamActivity($user, $team, $activity);

		if (is_null($category)) {
			return $hasTeamActivity;
		}

		return $hasTeamActivity && $this->user->hasTeamCategory($user, $team, $category);
	}

}

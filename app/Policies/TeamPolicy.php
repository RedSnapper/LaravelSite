<?php

namespace App\Policies;

use App\Policies\Helpers\UserPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

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

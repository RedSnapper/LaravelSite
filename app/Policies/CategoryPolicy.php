<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\Team;
use App\Models\User;
use App\Policies\Helpers\UserPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class CategoryPolicy {
	use HandlesAuthorization;

	/**
	 * @var UserPolicy
	 */
	private $user;

	/**
	 * CategoryPolicy constructor.
	 *
	 * @param Collection $categories
	 * @param Connection $connection
	 */
	public function __construct(UserPolicy $user) {
		$this->user = $user;
	}

	public function access(User $user,Category $category,Team $team = null) {
		if (is_null($team)) {
			return $this->user->hasCategory($user, $category, UserPolicy::CAN_ACCESS);
		} else {
			return $this->user->hasTeamCategory($user, $team, $category , UserPolicy::CAN_ACCESS);
		}
	}

	public function modify(User $user, Category $category,Team $team = null) {
		if (is_null($team)) {
			return $this->user->hasCategory($user, $category, UserPolicy::CAN_MODIFY);
		} else {
			return $this->user->hasTeamCategory($user, $team, $category , UserPolicy::CAN_MODIFY);
		}
	}

	public function __call($activity, $arguments) {
		list($user, $category, $team , $modify) = array_pad($arguments, 4, null);

		if(is_null($modify)) {
			$bits = explode('_',$activity);
			$modify = (count($bits) > 1) && ($bits[1] === 'MODIFY') ? UserPolicy::CAN_MODIFY : UserPolicy::CAN_ACCESS;
		}

		if (is_null($team)) {
			return Gate::allows($activity) && $this->user->hasCategory($user, $category, $modify ?? UserPolicy::CAN_ACCESS);
		} else {
			return $this->user->hasTeamCategory($user, $team, $category , $modify ?? UserPolicy::CAN_ACCESS);
		}
	}

}

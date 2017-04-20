<?php

namespace App\Policies;

use App\Models\Category;
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

	public function view(User $user, Category $category) {
		return $this->user->hasCategory($user, $category);
	}

	public function modify(User $user, Category $category) {
		return $this->user->hasCategory($user, $category);
	}

	public function __call($activity, $arguments) {

		list($user, $category, $team) = array_pad($arguments, 3, null);

		if (is_null($team)) {
			return Gate::allows($activity) && $this->user->hasCategory($user, $category);
		}

		return $this->user->hasTeamActivity($user, $team, $activity) && $this->user->hasTeamCategory($user, $team, $category);

	}

}

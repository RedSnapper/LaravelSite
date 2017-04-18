<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\Team;
use App\Models\User;
use App\Policies\Helpers\PolicyData;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;

class TeamPolicy {
	use HandlesAuthorization;
	/**
	 * @var PolicyData
	 */
	private $data;

	/**
	 * TeamPolicy constructor.
	 *
	 * @param Collection $teams
	 * @param Connection $connection
	 */
	public function __construct(PolicyData $data) {
		$this->data = $data;
	}

	public function view(User $user, Team $team){
		return $this->data->hasUserTeamActivity($user,$team);
	}

	public function update(User $user, Team $team){
		return $this->data->hasUserTeamActivity($user,$team);
	}

	public function __call($activity, $arguments) {
		switch(count($arguments)) {
			case 2:
				list($user,$team) = $arguments;
				return $this->data->hasUserTeamActivity($user,$team,$activity);
			break;
			case 3:
				list($user,$team,$category) = $arguments;
				$category = is_a($category,Category::class) ? $category->id : (int) $category;
				return Gate::allows($activity,$team->id) && $this->data->hasUserTeamCategory($user,$team,$category->id);
			break;
			default: return false;
		}
	}

}

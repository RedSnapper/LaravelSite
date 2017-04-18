<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\Team;
use App\Models\User;
use App\Policies\Helpers\PolicyData;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class CategoryPolicy
{
    use HandlesAuthorization;

	/**
	 * @var PolicyData
	 */
	private $data;

	/**
	 * CategoryPolicy constructor.
	 *
	 * @param Collection $categories
	 * @param Connection $connection
	 */
	public function __construct(PolicyData $data) {
		$this->data = $data;
	}

	public function view(User $user, Category $category){
		return $this->data->isCategoryAvailable($user,$category);
	}

	public function update(User $user, Category $category){
		return $this->data->isCategoryAvailable($user,$category);
	}


	public function __call($activity, $arguments) {
		switch(count($arguments)) {
			case 2:
				list($user,$category) = $arguments;
				return Gate::allows($activity) && $this->data->isCategoryAvailable($user,$category);
			break;
			case 3:
				list($user,$category,$team) = $arguments;
				$team = is_a($team,Team::class) ? $team->id : (int) $team;
				return Gate::allows($activity,$team) && $this->data->hasUserTeamCategory($user,$team,$category->id);
			break;
			default: return false;
		}
	}



}

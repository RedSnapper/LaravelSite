<?php

namespace App\Http\Formlets;

use App\Models\Category;
use App\Models\Role;
use App\Policies\Helpers\UserPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use RS\Form\Formlet;

class RoleFormlet extends Formlet {
	public $formView = "role.form";

	public function __construct(Role $role) {
		$this->setModel($role);
	}

	/**
	 * TODO: The base checkbox failed:- it is always returning true. So FTM changed this to a pure composite.
	 */
	public function prepareForm() {
		$this->addFormlet('role',RoleRecordFormlet::class)->setModel($this->model);
		$this->addSubscribers('activities', RoleActivityFormlet::class, $this->model->activities());
		$this->addSubscribers('categories', RoleCategoryFormlet::class, $this->model->categories()->withPivot('modify'), Category::ordered()->get());
	}


	public function edit(): Model {

		$role = $this->getFormlet('role')->edit();
		$role->activities()->sync($this->getSubscriberFields('activities'));

		$closure = function (Collection $collection): Collection {
			return $collection->filter(
				function ($array) {
					return ((int)$array['modify'] !== UserPolicy::INHERITING);
				}
			);
		};
		$catSubs = $this->getSubscriberFields('categories', 'modify', $closure);
		$role->categories()->sync($catSubs);
		return $role;
	}

	public function persist(): Model {
		return $this->edit();
	}
}
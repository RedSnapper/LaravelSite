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

	public function prepareForm() {
		$this->addFormlet('role', RoleRecordFormlet::class)->setModel($this->model);

		//Activities
		$this->addSubscribers('activities', RoleActivityFormlet::class, $this->model->activities());

		//Now do Categories.
		$subscribeOptions = Category::ordered()->get();
		$subscribedModels = $this->model->categories()->withPivot('modify')->get();
		foreach ($subscribeOptions as $option) {
			$subscribed = $this->getModelByKey($option->getKey(), $subscribedModels);
			$formlet = $this->addFormlets('categories', RoleCategoryFormlet::class); //despite the name, addFormlets adds 1 formlet.
			$formlet->setKey($option->getKey());
			if(!is_null($subscribed)) {
				$formlet->setModel($subscribed->pivot);
			}
			$formlet->with('option', $option);
		}
	}

	public function edit(): Model {

		//Do main role.
		$role = $this->getFormlet('role')->edit();

		//Do activities
		$role->activities()->sync($this->getSubscriberFields('activities'));

		//Do categories
		$categoriesToFilter = new Collection($this->fields('categories'));
		$categories = $categoriesToFilter->filter(
			function ($array) {
				return ((int)$array['modify'] !== UserPolicy::INHERITING);
			}
		);

		$role->categories()->sync($categories);
		return $role;
	}

	public function persist(): Model {
		return $this->edit();
	}
}
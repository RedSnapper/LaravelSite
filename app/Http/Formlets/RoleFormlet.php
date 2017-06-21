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

	public function prepareForm() : void {
		$this->addFormlet('role', RoleRecordFormlet::class)->setModel($this->model);

		//Activities
		$this->addSubscribers('activities', RoleActivityFormlet::class, $this->model->activities());

		//Now do Categories.
		$this->addSubscribers('categories', RoleCategoryFormlet::class, $this->model->categories());
	}

	public function edit(): Model {

		//Do main role.
		$role = $this->getFormlet('role')->edit();

		//Do activities
		$this->subs($this->getFormlets('activities'));

		//Do categories
		$this->subs($this->getFormlets('categories'));

		return $role;
	}

	public function persist(): Model {

		//Do main role.
		$role = $this->getFormlet('role')->persist();
		$this->setModel($role);

		//Do activities
		$this->subs($this->getFormlets('activities'));

		//Do categories
		$this->subs($this->getFormlets('categories'));

		return $role;
	}

}
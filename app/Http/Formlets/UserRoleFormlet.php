<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 22/03/2017 10:53
 */

namespace App\Http\Formlets;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use RS\Form\Fields\Checkbox;
use RS\Form\Formlet;

/**
 * Class UserRoleFormlet
 * An alternative to using the subscriber methods.
 *
 *
 * @package App\Http\Formlets
 */
//$allRoles = $this->model->roles()->unteamed()->get();

class UserRoleFormlet extends Formlet {
	public function prepareForm() {
		$allRoles = Role::unteamed()->get();
		foreach ($allRoles as $role) {
			$this->add((new Checkbox())//we don't want a name, because we are using sync below.
			  ->setLabel($role->name)
				->setValue($role->id)
			);
		}
	}

	public function edit(): Model {
		$fields = $this->fields();
		$this->model->roles()->sync($fields);
		return $this->model;
	}

	public function persist(): Model {
		return $this->edit();
	}
}

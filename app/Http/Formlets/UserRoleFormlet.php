<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 22/03/2017 10:53
 */

namespace App\Http\Formlets;

use RS\Form\Formlet;
use RS\Form\Fields\Checkbox;
use Illuminate\Database\Eloquent\Model;

class UserRoleFormlet extends Formlet {

	public function prepareForm(){
		$allRoles = $this->model->roles()->getRelated()->teamed(false)->get();
		foreach ($allRoles as $role) {
			$this->add((new Checkbox()) //we don't want a name, because we are using sync below.
				->setLabel($role->name)
				->setValue($role->id)
			);
		}
	}

	public function edit() : Model {
		$this->model->roles()->sync($this->fields());
		return $this->model;
	}

	public function persist(): Model {
		return $this->edit();
	}


}
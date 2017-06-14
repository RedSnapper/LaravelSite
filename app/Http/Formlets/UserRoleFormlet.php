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

class UserRoleFormlet extends Formlet {

	protected $subscriber = "roleSubscription";

	public function prepareForm() : void{
		$field = new Checkbox('roleSubscription');
		$label = $this->getData('option.name');
		$field->setLabel($label);
		$this->add($field);
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

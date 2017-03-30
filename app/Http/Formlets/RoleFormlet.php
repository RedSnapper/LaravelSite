<?php

namespace App\Http\Formlets;

use RS\Form\Formlet;
use RS\Form\Fields\Input;
use App\Models\Role;

class RoleFormlet extends Formlet {

	public $formView = "role.form";

	public function __construct(Role $role) {
		$this->setModel($role);
	}

	public function prepareForm(){
		$field = new Input('text','name');
		$this->add(
			$field->setLabel('Name')->setRequired()
		);
	}

	public function rules():array{
		return [
			'name' => 'required|max:255',
		];
	}

}
<?php

namespace App\Http\Formlets;
use App\Http\Fields\Input;
use App\Role;

class RoleFormlet extends Formlet {

	protected $formView = "role.form";
	protected $role;

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
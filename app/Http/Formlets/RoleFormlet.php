<?php

namespace App\Http\Formlets;
use App\Http\Fields\Input;
use App\Http\Fields\Select;
use App\Models\Role;
use App\Models\Category;

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

		$field = new Select('category_id',Category::options('ROLES'));
		$this->add($field->setLabel("Category"));

	}

	public function rules():array{
		return [
			'name' => 'required|max:255',
		];
	}

}
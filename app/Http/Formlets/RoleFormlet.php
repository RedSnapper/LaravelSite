<?php

namespace App\Http\Formlets;
use App\Http\Fields\Checkbox;
use App\Http\Fields\Input;
use App\Role;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class RoleFormlet extends Formlet {

	protected $formView = "role.form";
	protected $role;

	public function __construct(Role $role) {
		$this->role = $role;
	}

	public function prepareModels() {
		$role = $this->role->find($this->getKey());
		$this->setModel($role); //needed for the unique email.
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

	public function edit(): Model {
		$this->model->fill($this->fields());
		$this->model->save();
		return $this->model;
	}


	public function persist():Model {
		$role = $this->role->create($this->fields());
		return $role;
	}

}
<?php

namespace App\Http\Formlets;
use App\Role;
use Illuminate\Database\Eloquent\Model;

class RoleComposite extends Formlet {
	/**
	 * @var Role|null
	 */
	protected $role = null;
	protected $view = "role.composite";
	protected $formView = "role.form";

	public function __construct(Role $role) {
		$this->role = $role;
	}

	public function setCreating(bool $creating = false) {
		$this->creating = $creating;
	}

	public function prepareForm(){
		$role = $this->role->find($this->getKey());
		$this->addFormlet('role',RoleFormlet::class)->setModel($role);
		$this->addFormlet('users',RoleUserFormlet::class)->setModel($role->users);
	}

	//update
	public function edit(): Model {
		$role = $this->formlets['role']->getModel();
		$role->users()->sync($this->fields('users'));
		$role->fill($this->fields('role'))->save();
		return $role;
	}

	//new
	public function persist():Model {
		$role = $this->role->create($this->fields('role'));
		return $role;
	}

}
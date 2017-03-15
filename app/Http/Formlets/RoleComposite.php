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

	protected function prepareModels() {
		$role = $this->role->find($this->getKey());
		$this->addModel('role',$role); //needed for the unique email.
		$this->addModel('users',$role->users);
	}

	public function prepareForm(){
		$this->addFormlet('role',RoleFormlet::class);
		$this->addFormlet('users',RoleUserFormlet::class);
	}

	//update
	public function edit(): Model {
		$role = $this->models['role'];
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
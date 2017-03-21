<?php

namespace App\Http\Formlets;
use App\Role;
use Illuminate\Database\Eloquent\Model;

class RoleComposite extends Formlet {

	protected $view = "role.composite";
	protected $formView = "role.form";

	public function prepareForm(){

		if($this->getKey()){
			$this->addFormlet('role',RoleFormlet::class)->setKey($this->key);
			$role = $this->getModel('role');
			$this->addFormlet('users',RoleUserFormlet::class)->setModel($role->users);
		}else{
			$this->addFormlet('role',RoleFormlet::class);
		}

	}

	//update
	public function edit(): Model {
		$role = $this->getModel('role');

		$role->users()->sync($this->fields('users'));
		$role->fill($this->fields('role'))->save();
		return $role;
	}

	//new
	public function persist():Model {
		$role = $this->getModel('role');
		$role = $role->create($this->fields('role'));
		return $role;
	}

}
<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 14/03/2017 15:36
 */

namespace App\Http\Formlets;

//Which roles does a user have?
use App\Http\Fields\Checkbox;
use App\Http\Fields\Input;
use App\Role;
use App\User;

class RoleUserFormlet extends Formlet {
	protected $role;
	protected $user;

	public function __construct(Role $role,User $user) {
		$this->role = $role;
		$this->user = $user;
	}


	public function prepareForm(){

		$roles = Role::all();
		foreach ($roles as $role) {
			$this->add((new Checkbox('role[]',$role->id))
				->setLabel($role->name)
			);
		}

	}

}
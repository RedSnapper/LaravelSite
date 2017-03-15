<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 14/03/2017 15:36
 */

namespace App\Http\Formlets;

//Which roles does a user have?
use App\Http\Fields\Checkbox;
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
		$model =  $this->getName() == 'roles' ? $this->role : $this->user;
		$items = $model->all();
		foreach ($items as $item) {
			$this->add((new Checkbox('',$item->id))
				->setLabel($item->name)
			);
		}
	}

}
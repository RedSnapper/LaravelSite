<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 13/04/2017 10:29
 */

namespace App\Http\Formlets;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use RS\Form\Fields\Select;
use RS\Form\Formlet;

class TeamUserFormlet extends Formlet {
	public $formletView = "team.user";

	/**
	 * Prepare the form with fields
	 *
	 * @return void
	 */
	public function prepareForm() {
		$field = new Select('role[]',Role::options());
		$this->add(
			$field->setMultiple(true)
			->size(5)
			->setValue($this->model->teamRoles->pluck('id')->all())
		);
	}

	//public function edit(): Model {
	//	//dd($this->model->teamRoles);
	//	//$user->roles()->sync([1,2,3], false);
	//	$role = parent::edit();
	//	return $role;
	//}
	//
}
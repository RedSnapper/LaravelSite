<?php

namespace App\Http\Formlets;

use App\Policies\Helpers\UserPolicy;
use RS\Form\Fields\Select;
use RS\Form\Formlet;

class RoleCategoryFormlet extends Formlet {
	public $formletView = "role.category";

	/**
	 * Prepare the form with fields
	 *
	 * @return void
	 */
	public function prepareForm() {
		$select = new Select('modify', [
			UserPolicy::CAN_ACCESS => 'Access',
			UserPolicy::CAN_MODIFY => 'Modify',
			UserPolicy::INHERITING => 'Inherit',
			UserPolicy::NIL_ACCESS => 'None'
		]);
		$this->add($select->setDefault(UserPolicy::INHERITING));
	}
}

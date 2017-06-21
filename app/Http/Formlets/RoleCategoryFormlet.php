<?php

namespace App\Http\Formlets;

use App\Policies\Helpers\UserPolicy;
use RS\Form\Fields\AbstractField;
use RS\Form\Fields\Select;
use RS\Form\Formlet;

class RoleCategoryFormlet extends Formlet {
	public $formletView = "role.category";
	protected $subscriber = "modify";

	/**
	 * Prepare the form with fields
	 *
	 * @return void
	 */
	public function prepareForm() : void {
		$field = new Select('modify', [
			UserPolicy::CAN_ACCESS => 'Access',
			UserPolicy::CAN_MODIFY => 'Modify',
			UserPolicy::INHERITING => 'Inherit',
			UserPolicy::NIL_ACCESS => 'None'
		]);
		$field->setValueType(AbstractField::TYPE_INT)->setUnChecked(UserPolicy::INHERITING);
		$field->setMultiple(false)->setDefault(UserPolicy::INHERITING);
		$this->add($field);
	}
}

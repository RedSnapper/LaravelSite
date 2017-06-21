<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 05/04/2017 10:43
 */

namespace App\Http\Formlets;

use RS\Form\Fields\AbstractField;
use RS\Form\Formlet;
use RS\Form\Fields\Checkbox;

class ActivityRoleFormlet extends Formlet {
	protected $subscriber = 'subscriber';
	public $formletView = "activity.role";

	public function prepareForm() : void {
		$this->add((new Checkbox('subscriber'))->setValueType(AbstractField::TYPE_INT));
	}
}
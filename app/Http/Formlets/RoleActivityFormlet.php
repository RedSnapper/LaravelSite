<?php

namespace App\Http\Formlets;

use RS\Form\Fields\AbstractField;
use RS\Form\Fields\Checkbox;
use RS\Form\Formlet;

class RoleActivityFormlet extends Formlet{

	public $formletView = "role.activity";
	protected $subscriber = 'subscriber';

	/**
    * Prepare the form with fields
    *
    * @return void
    */
	public function prepareForm() : void {

		$field = (new Checkbox('subscriber'))->setValueType(AbstractField::TYPE_BOOL)->setValue(true);
		$this->add($field);
	}

}

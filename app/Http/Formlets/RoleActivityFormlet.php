<?php

namespace App\Http\Formlets;

use RS\Form\Fields\Checkbox;
use RS\Form\Formlet;

class RoleActivityFormlet extends Formlet{

	public $formletView = "role.activity";

	//Used to mark which field is used for subscriptions.
	protected $subscriber = 'subscriber';


	/**
    * Prepare the form with fields
    *
    * @return void
    */
	public function prepareForm() {
		$this->add((new Checkbox('subscriber')));
	}

}

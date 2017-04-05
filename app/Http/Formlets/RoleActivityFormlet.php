<?php

namespace App\Http\Formlets;

use RS\Form\Fields\Checkbox;
use RS\Form\Formlet;

class RoleActivityFormlet extends Formlet{

	public $formletView = "role.activity";

   /**
    * Prepare the form with fields
    *
    * @return void
    */
	public function prepareForm() {
		$this->add((new Checkbox('subscriber')));
	}

}

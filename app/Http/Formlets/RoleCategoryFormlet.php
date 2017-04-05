<?php

namespace App\Http\Formlets;

use RS\Form\Fields\Checkbox;
use RS\Form\Formlet;

class RoleCategoryFormlet extends Formlet {

	public $formletView = "role.category";

	/**
	 * Prepare the form with fields
	 *
	 * @return void
	 */
	public function prepareForm() {
		$this->add((new Checkbox('subscriber')));
	}

}

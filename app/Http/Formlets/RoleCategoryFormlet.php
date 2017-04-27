<?php

namespace App\Http\Formlets;

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
		$select = new Select('modify', ['0' => 'Access', '1' => 'Modify', '2' => 'Inherit']);
		$this->add($select->setDefault(2));
	}

}

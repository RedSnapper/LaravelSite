<?php

namespace App\Http\Formlets;

use RS\Form\Formlet;

class UserNested extends Formlet {

	public $compositeView = "user.composite";

	public $formView = "user.form";

	public function prepareForm() : void {
		$this->addFormlet('user',UserFormlet::class);
		$this->addFormlet('profile',UserComposite::class);
	}

}
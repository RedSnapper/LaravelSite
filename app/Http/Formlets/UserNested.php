<?php

namespace App\Http\Formlets;

class UserNested extends Formlet {

	public $compositeView = "user.composite";

	public $formView = "user.form";

	public function prepareForm() {
		$this->addFormlet('user',UserFormlet::class);
		$this->addFormlet('profile',UserComposite::class);
	}

}
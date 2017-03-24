<?php

namespace App\Http\Formlets;

class UserNested extends Formlet {

	protected $compositeView = "user.composite";

	protected $formView = "user.form";

	public function prepareForm() {
		$this->addFormlet('user',UserFormlet::class);
		$this->addFormlet('profile',UserComposite::class);
	}

}
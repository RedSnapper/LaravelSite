<?php

namespace App\Http\Formlets;

use RS\Form\Formlet;
use RS\Form\Fields\Input;

class RegisterForm extends Formlet {
	public $formView = "auth.register";
	protected $guarded = ['password', 'password_confirmation'];

	public function prepareForm() {

		$this->add((new Input('text', 'name'))
		  ->setLabel("Name")
		  ->setRequired()
		  ->autofocus()
		);

		$this->add((new Input('email', 'email'))
		  ->setLabel("E-Mail Address")
		  ->setRequired()
		);

		$this->add((new Input('password', 'password'))
		  ->setLabel("Password")
		  ->setRequired()
		);

		$this->add((new Input('password', 'password_confirmation'))
		  ->setLabel("Confirm Password")
		  ->setRequired()
		);
	}

	public function rules(): array {
		return [];
	}

}
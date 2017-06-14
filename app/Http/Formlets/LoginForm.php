<?php

namespace App\Http\Formlets;

use RS\Form\Formlet;
use RS\Form\Fields\Checkbox;
use RS\Form\Fields\Input;

class LoginForm extends Formlet {

	public $formView = "auth.login";

	protected $guarded = ['password'];

	public function prepareForm() : void {

		$field = new Input('email', 'email');
		$this->add(
		  $field->setLabel('E-Mail Address')
			->setRequired()
			->autofocus()
		);

		$field = new Input('password', 'password');
		$this->add(
		  $field->setLabel('Password')
			->setRequired()
		);

		$field = new Checkbox('remember');
		$this->add(
		  $field->setLabel('Remember Me')
		);

	}

}
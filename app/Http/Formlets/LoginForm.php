<?php

namespace App\Http\Formlets;

use App\Http\Fields\Checkbox;
use App\Http\Fields\Input;

class LoginForm extends Formlet {

	protected $formView = "auth.login";

	protected $guarded = ['password'];

	public function prepareForm() {

		$field = new Input('email', 'email');
		$this->add(
		  $field->setLabel('E-Mail Address')
			//->setRequired()
			->autofocus()
		);

		$field = new Input('password', 'password');
		$this->add(
		  $field->setLabel('Password')
			//->setRequired()
		);

		$field = new Checkbox('remember');
		$this->add(
		  $field->setLabel('Remember Me')
		);

	}

	public function rules(): array {
		return [];
	}



}
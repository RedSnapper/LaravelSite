<?php
/**
 * Created by PhpStorm.
 * User: param
 * Date: 13/03/2017
 * Time: 08:52
 */

namespace App\Http\Formlets;

use App\Http\Fields\Input;

class RegisterForm extends Formlet {

	protected $formView = "auth.register";

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
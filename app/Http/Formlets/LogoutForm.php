<?php
/**
 * Created by PhpStorm.
 * User: param
 * Date: 13/03/2017
 * Time: 08:52
 */

namespace App\Http\Formlets;

class LogoutForm extends Formlet {

	protected $formView = "auth.logout";

	public function prepareForm() {

	}

	public function rules(): array {
		return [];
	}

}
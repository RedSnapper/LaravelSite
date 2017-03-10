<?php
/**
 * Created by PhpStorm.
 * User: param
 * Date: 10/03/2017
 * Time: 16:12
 */

namespace App\Http\Formlets;

class UserNested extends Formlet {

	protected $view = "user.composite";

	protected $formView = "user.form";

	public function prepareForm() {
		$this->addFormlet(UserFormlet::class, 'user');
		$this->addFormlet(UserComposite::class, 'bar');
	}

	public function rules(): array {
		return [];
	}
}
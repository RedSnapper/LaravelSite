<?php
/**
 * Created by PhpStorm.
 * User: param
 * Date: 10/03/2017
 * Time: 16:12
 */

namespace App\Http\Formlets;

class UserNested extends Formlet {

	protected $compositeView = "user.composite";

	protected $formView = "user.form";

	public function prepareForm() {
		$this->addFormlet('user',UserFormlet::class);
		$this->addFormlet('profile',UserComposite::class);
	}

}
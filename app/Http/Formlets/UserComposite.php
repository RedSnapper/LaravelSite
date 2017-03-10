<?php
/**
 * Created by PhpStorm.
 * User: param
 * Date: 10/03/2017
 * Time: 15:31
 */

namespace App\Http\Formlets;

class UserComposite extends Formlet {

	protected $view = "user.composite";

	protected $formView = "user.form";

	public function prepareForm(){
		$this->addFormlet(UserFormlet::class,'user');
		$this->addFormlet(UserFormlet::class,'bar');
	}

	public function rules():array{
		return [];
	}
}
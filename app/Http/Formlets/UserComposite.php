<?php
/**
 * Created by PhpStorm.
 * User: param
 * Date: 10/03/2017
 * Time: 15:31
 */

namespace App\Http\Formlets;

use Illuminate\Database\Eloquent\Model;

class UserComposite extends Formlet {

	protected $view = "user.composite";

	protected $formView = "user.form";

	public function prepareForm(){
		$this->addFormlet(UserFormlet::class,'user');
		$this->addFormlet(UserProfileForm::class,'profile');
	}

	public function rules():array{
		return [];
	}

	public function persist():Model {
		dd($this->fields());
	}

}
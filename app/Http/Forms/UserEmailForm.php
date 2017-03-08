<?php
/**
 * Created by PhpStorm.
 * User: param
 * Date: 08/03/2017
 * Time: 12:22
 */

namespace App\Http\Forms;

use App\Http\Formlets\UserEmailFormlet;

class UserEmailForm extends Form {

	protected $view = "user.form";

	public function prepare() {
		$this->add(UserEmailFormlet::class,'user');
	}





}
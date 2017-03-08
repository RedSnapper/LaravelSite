<?php
/**
 * Created by PhpStorm.
 * User: param
 * Date: 08/03/2017
 * Time: 12:22
 */

namespace App\Http\Forms;

use App\Http\Formlets\UserFormlet;

class UserForm extends Form {

	protected $view = "user.form";

	public function prepare() {

		$this->add(UserFormlet::class,'user');

	}





}
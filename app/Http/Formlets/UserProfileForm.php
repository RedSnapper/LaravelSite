<?php

namespace App\Http\Formlets;

use App\Http\Fields\Input;
use App\UserProfile;

class UserProfileForm extends Formlet {
	protected $formView = "user.form";

	public function __construct(UserProfile $profile) {
		$this->setModel($profile);
	}

	public function prepareForm(){
		$field = new Input('tel','telephone');
		$this->add(
		  $field->setLabel('Telephone')
		);
	}

	public function rules():array{
		return [
		  'telephone' => 'required|max:255'
		];
	}



}


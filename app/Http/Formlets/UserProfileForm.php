<?php

namespace App\Http\Formlets;

use RS\Form\Formlet;
use RS\Form\Fields\Input;
use App\Models\UserProfile;

class UserProfileForm extends Formlet {
	public $formView = "user.form";

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


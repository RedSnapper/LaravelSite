<?php

namespace App\Http\Formlets;
use App\Http\Fields\Input;
use Illuminate\Validation\Rule;

class UserEmailForm extends Formlet {

	protected $view = "forms.user.email";

	public function prepareForm(){

		$field = new Input('text','name');
		$this->add($field->setLabel('Name'));

		$field = new Input('email','email');
		$this->add($field->setLabel('Email'));

	}

	public function rules():array{

		return [
		  'name' => 'required|max:255',
		  'email' => ['required','email','max:255',Rule::unique('users')->ignore($this->model->id)]
		];
	}


}
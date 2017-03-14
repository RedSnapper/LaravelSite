<?php

namespace App\Http\Formlets;
use App\Http\Fields\Input;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class UserEmailFormlet extends Formlet {
	
	protected $formView = "user.form";

	public function prepareForm(){

		$field = new Input('text','name');
		$this->add($field->setLabel('Name'));

		$field = new Input('email','email');
		$this->add($field->setLabel('Email'));

	}

	public function rules():array{

		return [
		  'name' => 'required|max:255',
		  'email' => ['required','email','max:255',Rule::unique('users')->ignore($this->getKey())]
		];
	}

	public function edit():Model {

		$this->model->fill($this->fields());
		$this->model->save();

		return $this->model;
	}


}
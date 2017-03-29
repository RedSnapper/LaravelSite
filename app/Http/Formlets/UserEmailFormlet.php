<?php

namespace App\Http\Formlets;
use App\Http\Fields\Input;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class UserEmailFormlet extends Formlet {

	public $formView = "user.form";

	public function __construct(User $user) {
		$this->setModel($user);
	}

	public function prepareForm(){

		$field = new Input('text','name');
		$this->add($field->setLabel('Name'));

		$field = new Input('email','email');
		$this->add($field->setLabel('Email'));

	}

	public function rules():array{
		$key = $this->model->getKey();
		return [
		  'name' => 'required|max:255',
		  'email' => ['required','email','max:255',Rule::unique('users')->ignore($key)]
		];
	}

	public function edit(): Model {
		$this->model->fill($this->fields())->save();
		return $this->model;
	}


}
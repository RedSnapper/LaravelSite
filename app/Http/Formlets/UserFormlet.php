<?php

namespace App\Http\Formlets;

use RS\Form\Formlet;
use RS\Form\Fields\Input;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class UserFormlet extends Formlet {

	public $formView = "user.form";

	protected $guarded = ['password','password_confirmation'];



	public function __construct(User $user) {
		$this->setModel($user);
	}


	public function prepareForm() {

		$field = new Input('text','name');
		$this->add(
		  $field->setLabel('Name')
		);

		$field = new Input('email','email');
		$this->add($field->setLabel('Email'));

		$field = new Input('password','password');
		$this->add($field->setLabel('Password'));

		$field = new Input('password','password_confirmation');
		$this->add($field->setLabel('Confirm Password'));


	}

	public function rules():array{
		return [
		  'name' => 'required|max:255',
			'email' => ['required','email','max:255',Rule::unique('users')->ignore($this->getKey())],
		  'password' => 'required|min:6|confirmed'
		];
	}

	public function edit(): Model {
		$this->model->fill($this->fields());
		$this->model->save();
		return $this->model;
	}


	public function persist():Model {
		$user = User::create($this->fields());
		return $user;
	}




}
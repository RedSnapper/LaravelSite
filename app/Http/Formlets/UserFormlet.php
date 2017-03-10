<?php

namespace App\Http\Formlets;
use App\Http\Fields\Input;
use App\User;
use Illuminate\Database\Eloquent\Model;

class UserFormlet extends Formlet {



	protected $view = "user.create";

	protected $formView = "user.form";

	protected $guarded = ['password','password_confirm'];

	protected $user;


	public function __construct(User $user) {
		$this->user = $user;
	}

	public function prepareForm(){

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
		  'email' => 'required|email|max:255|unique:users',
		  'password' => 'required|min:6|confirmed'
		];
	}

	public function persist():Model {

		$this->model->fill($this->fields());
		$this->model->save();

		return $this->model;
	}


}
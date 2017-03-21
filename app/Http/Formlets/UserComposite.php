<?php

namespace App\Http\Formlets;

use Illuminate\Database\Eloquent\Model;

class UserComposite extends Formlet {

	protected $view = "user.composite";
	protected $formView = "user.form";

	public function prepareForm(){
		if(!isset($this->key)) {
			$this->addFormlet('user',UserFormlet::class);
		} else {
			$this->addFormlet('user',UserEmailFormlet::class)->setKey($this->key);
		}
		$user = $this->getModel('user'); //gets model from formlet 'user'.
		$this->addFormlet('profile',UserProfileForm::class)->setModel($user->profile);
		$this->addFormlet('roles',RoleUserFormlet::class)->setModel($user->roles);
	}

	//update
	public function edit() : Model {
		$user = $this->getModel('user'); //gets model from formlet 'user'.
		$user->fill($this->fields('user'))->save();
		$user->roles()->sync($this->fields('roles'));
		$user->profile->fill($this->fields('profile'))->save();
		return $user;
	}

	//new
	public function persist(): Model {
		$user = $this->getModel('user'); //gets model from formlet 'user'. (same as $this->model)
		$user = $user->create($this->fields('user'));
		$user->roles()->sync($this->fields('roles'));
		$user->profile()->create($this->fields('profile'));
		return $user;
	}

}
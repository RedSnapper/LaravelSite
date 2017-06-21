<?php

namespace App\Http\Formlets;

use App\Models\User;
use RS\Form\Formlet;
use Illuminate\Database\Eloquent\Model;

class UserComposite extends Formlet {

	public $compositeView = "user.composite";
	public $formView = "user.form";

	public function prepareForm() : void{
		if(!isset($this->model)) {
			$this->addFormlet('user',UserFormlet::class);
			$this->model = $this->getModel('user');//->with('profile');
		} else {
			$this->addFormlet('user',UserEmailFormlet::class)->setModel($this->model);
		}

		//Set UserProfile
		$this->addFormlet('profile',UserProfileForm::class)->setModel($this->model->profile()->first());

		//Set UserRoles :070615 working both ways. //now failing when all are unchecked...
		$this->addSubscribers('roles',UserRoleFormlet::class,$this->model->roles());

		//Set UserTeamRoles (roles over team) //currently fails for load 2017-06-21
		$this->addSubscribers('teams',TeamRolesFormlet::class,$this->model->teamRoles());

	}

	//update
	public function edit(): Model {
		/** @var User $user */
		$user = $this->getFormlet('user')->edit();
		$this->getFormlet('profile')->edit();

		$this->subs($this->getFormlets('roles'));

		$this->subs($this->getFormlets('teams'));

		return $user;
	}

}
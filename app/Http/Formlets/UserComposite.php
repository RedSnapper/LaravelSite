<?php

namespace App\Http\Formlets;

use App\Models\Role;
use App\Models\Team;
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

		//Set UserRoles (TODO: failing: save.)
		$this->addSubscribers('roles',UserRoleFormlet::class,$this->model->roles());

		//Set UserTeamRoles (roles over team) (TODO: failing both in load and save.)
		/**
		 * $this->model->teamRoles()->get()
		 * returns eg 5 results, with the role_id,user_id available via the pivot data.
		 *
		+---------+---------+---------+
		| team_id | role_id | user_id |
		+---------+---------+---------+
		|       1 |       3 |       1 |
		|       2 |       3 |       1 |
		|       2 |       4 |       1 |
		|       3 |       3 |       1 |
		|       3 |       4 |       1 |
		+---------+---------+---------+
		 * addSubscribers correctly derives the teams from this (including the fact that they are team_based).
		**/
		$this->addSubscribers('teams',TeamRolesFormlet::class,$this->model->teamRoles());

	}

	//update
	public function edit(): Model {
		/** @var User $user */
		$user = $this->getFormlet('user')->edit();
		$this->getFormlet('profile')->edit();

		$subData = $this->subs($this->getFormlets('roles'));
		$user->roles()->sync($subData);

		$subData = $this->subs($this->getFormlets('teams'));
		$user->roleTeams()->sync($subData);

//		$this->syncUserRoles($user);
		return $user;
	}

}
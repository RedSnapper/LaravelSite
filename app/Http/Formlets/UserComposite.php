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

		//Set UserRoles :070615 working both ways.
		$this->addSubscribers('roles',UserRoleFormlet::class,$this->model->roles());

		//Set UserTeamRoles (roles over team) (TODO: failing save.)
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

		//The following isn't working.. need to find out why.
		//Probably because subs doesn't know how to handle multi-values?
		$subData = $this->subs($this->getFormlets('teams'));
		print("cf. Formlet line 207. We need to sync multiple values against multiple values..");
		print("<br />Also cf. User model, where the old sync did it across each team...");
		print("<br />I think that we should use the Builder (passed in the addSubs above) to derive the sync anyhow...");
		print("<br />Then we could do multi statements if necessary...");
		dd($subData);
		$user->teamRoles()->sync($subData);

		return $user;
	}

}
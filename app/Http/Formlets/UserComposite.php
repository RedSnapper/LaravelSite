<?php

namespace App\Http\Formlets;

use App\Models\Team;
use App\Models\User;
use RS\Form\Formlet;
use Illuminate\Database\Eloquent\Model;

class UserComposite extends Formlet {

	public $compositeView = "user.composite";
	public $formView = "user.form";

	public function prepareForm(){
		if(!isset($this->model)) {
			$this->addFormlet('user',UserFormlet::class);
			$this->model = $this->getModel('user');//->with('profile');
		} else {
			$this->addFormlet('user',UserEmailFormlet::class)->setModel($this->model);
		}

		//Set UserProfile
		$this->addFormlet('profile',UserProfileForm::class)->setModel($this->model->profile()->first());

		//Set UserRoles
		$roles = $this->model->roles()->get();
		$this->model->setRelation('roles',$roles);
		$this->addFormlet("roles",UserRoleFormlet::class)->setModel($this->model);

		//Set UserTeamRoles
		$teams = Team::withUser($this->model)->get();
		foreach ($teams as $team) {
			$this->addFormlets("teams",TeamRolesFormlet::class)
				->setModel($team);
		}
	}

	//update
	public function edit(): Model {
		$user = $this->getFormlet('user')->edit();
		$this->getFormlet('roles')->edit();
		$this->getFormlet('profile')->edit();
		$this->syncTeamRoles($user);

		return $user;
	}

	public function persist():Model {

		$user = $this->getFormlet('user')->persist();

		// One query
		$profile = $this->getModel('profile');
		$profile->fill($this->fields('profile'));
		$profile->user()->associate($user);
		$profile->save();

		$this->syncTeamRoles($user);
		$this->getFormlet('roles')->setModel($user)->persist();
		return $user;
	}

	public function syncTeamRoles($user) {
		$teams = Team::get();
		foreach($teams as $teamModel) {
			$teamId = $teamModel->id;
			$roles = $this->fields("teams.role.$teamId");
			$user->syncTeamRoles($teamModel, $roles);
		}
	}

}
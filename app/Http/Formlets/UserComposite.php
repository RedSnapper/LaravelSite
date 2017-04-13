<?php

namespace App\Http\Formlets;

use App\Models\Team;
use RS\Form\Formlet;
use Illuminate\Database\Eloquent\Model;

class UserComposite extends Formlet {

	public $compositeView = "user.composite";
	public $formView = "user.form";

	public function prepareForm(){
		if(!isset($this->key)) {
			$this->addFormlet('user',UserFormlet::class);
		} else {
			$this->addFormlet('user',UserEmailFormlet::class)->setKey($this->key);
		}
		$model = $this->getModel('user');
		$this->addFormlet('profile',UserProfileForm::class)->setKey($this->key);
		$profile = $this->getModel('profile');
		$this->addFormlet('billing',AddressFormlet::class)->setKey($profile->billing_id); //setModel($profile->billing);
		$this->addFormlet('delivery',AddressFormlet::class)->setKey($profile->delivery_id); //setModel($profile->billing);
		$this->addFormlet('roles',Subscriber::class)->setModel($model);


		//Set UserTeamRoles
		$user = $this->key;
		$teams = Team::with([
			'userRoles' => function ($query) use ($user) {
				$query->wherePivot('user_id', $user);
			}
		])->get();
		foreach ($teams as $team) {
			$this->addFormlets("teams", TeamRolesFormlet::class)
				->setKey($team->getKey())
				->setModel($team);
		}

//		$this->addFormlet('teams',TeamRolesFormlet::class)->setModel($model);
	}

	//update
	public function edit(): Model {
		$user = $this->getFormlet('user')->edit();
		$this->getFormlet('roles')->edit();
		$this->getFormlet('profile')->edit();
		$this->getFormlet('billing')->edit();
		$this->getFormlet('delivery')->edit();
		$this->syncTeamRoles($user);

		return $user;
	}

	public function persist():Model {

		$billing = $this->getFormlet('billing')->persist();
		$delivery = $this->getFormlet('delivery')->persist();
		$user = $this->getFormlet('user')->persist();

		// One query
		$profile = $this->getModel('profile');
		$profile->fill($this->fields('profile'));
		$profile->user()->associate($user);
		$profile->delivery()->associate($delivery);
		$profile->billing()->associate($billing);
		$profile->save();

		$this->syncTeamRoles($user);
		$this->getFormlet('roles')->setModel($user)->persist();
		return $user;
	}

	public function syncTeamRoles($user) {
		$teams = Team::get();
		//we need all teams so that we can delete those which are set to empty.
		foreach($teams as $teamModel) {
			$team = $teamModel->id;
			$roles = $this->fields("teams.role.$team");
			$user->syncTeamRoles($team, $roles);
		}
	}

}
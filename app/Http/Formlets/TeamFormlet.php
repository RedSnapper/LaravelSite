<?php

namespace App\Http\Formlets;


use App\Http\Formlets\Helpers\CategoryHelper;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use RS\Form\Fields\Input;

use RS\Form\Formlet;

class TeamFormlet extends Formlet {
	public $formView = "team.form";
	/**
	 * @var CategoryHelper
	 */
	private $categoryHelper;

	public function __construct(Team $team,CategoryHelper $categoryHelper) {
		$this->setModel($team);
		$this->categoryHelper = $categoryHelper;
	}

	public function prepareForm() {
		$field = new Input('text', 'name');
		$this->add(
			$field->setLabel('Name')->setRequired()
		);

		$this->categoryHelper->field($this,'TEAMS');

		//Set UserTeamRoles
		$team = $this->getModel()->getKey();
		$users = User::with([
			'teamRoles' => function ($query) use ($team) {
				$query->wherePivot('team_id', $team);
			}
		])->get();
		foreach ($users as $user) {
			$this->addFormlets("users", TeamUserFormlet::class)
				->setKey($user->getKey())
				->setModel($user);
		}

	}

	public function rules(): array {
		return [
			'name'        => 'required|max:255',
			'category_id' => 'required|category'
		];
	}

	public function edit(): Model {
		$team = parent::edit();
		$users = User::get();
		//we need all users so that we can delete those which are set to empty.
		foreach($users as $userModel) {
			$user = $userModel->id;
			$roles = $this->fields("users.role.$user");
			$team->syncUserRoles($user, $roles);
		}
		return $team;
	}

	public function persist(): Model {
		return $this->edit();
	}
}
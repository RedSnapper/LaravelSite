<?php

namespace App\Http\Formlets;


use App\Http\Controllers\CategoryController;
use App\Http\Formlets\Helpers\CategoryHelper;
use App\Models\Team;
use Illuminate\Database\Eloquent\Model;
use RS\Form\Fields\Input;

use RS\Form\Formlet;

class TeamFormlet extends Formlet {
	public $formView = "team.form";
	private $categoryHelper = null;

	public function __construct(Team $team,CategoryHelper $categoryHelper) {
		$this->setModel($team);
		$this->categoryHelper = $categoryHelper;
	}


	public function rules(): array {
		return [
			'name'        => 'required|max:255',
			'category_id' => 'required|category'
		];
	}

	public function prepareForm() : void {
		$field = new Input('text', 'name');
		$this->add(
			$field->setLabel('Name')->setRequired()
		);
		$this->categoryHelper->field($this,"TEAMS");
		$this->addSubscribers('users',TeamRolesFormlet::class,$this->model->userRoles());
	}

	public function edit(): Model {
		$team = parent::edit();
		$this->subs($this->getFormlets('users'));
		return $team;
	}

}
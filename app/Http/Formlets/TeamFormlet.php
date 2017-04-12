<?php

namespace App\Http\Formlets;

use App\Models\Team;
use Illuminate\Database\Eloquent\Model;
use RS\Form\Formlet;
use RS\Form\Fields\Input;
use RS\Form\Fields\Select;
use App\Models\Category;

class TeamFormlet extends Formlet {

	public $formView = "team.form";

	public function __construct(Team $team) {
		$this->setModel($team);
	}

	public function prepareForm() {
		$field = new Input('text', 'name');
		$this->add(
			$field->setLabel('Name')->setRequired()
		);

		$field = new Select('category_id', Category::options('TEAMS'));
		$this->add(
			$field->setLabel("Category")
				->setPlaceholder("Please select a category")
				->setDefault($this->getData('category'))
		);

//		$this->addSubscribers('activities', TeamActivityFormlet::class, $this->model->activities());

//		$this->addSubscribers('categories', TeamCategoryFormlet::class, $this->model->categories(),Category::ordered()->get());

	}

	public function rules(): array {
		return [
			'name' => 'required|max:255',
			'category_id' => 'required'
		];
	}

	public function edit(): Model {

		$team = parent::edit();

//		$team->activities()->sync($this->getSubscriberFields('activities'));

//		$team->categories()->sync($this->getSubscriberFields('categories'));

		return $team;
	}

	public function persist(): Model {
		return $this->edit();
	}

}
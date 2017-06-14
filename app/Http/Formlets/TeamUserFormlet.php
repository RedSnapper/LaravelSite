<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 13/04/2017 10:29
 */

namespace App\Http\Formlets;

use App\Http\Controllers\CategoryController;
use App\Models\Role;
use RS\Form\Fields\Select;
use RS\Form\Formlet;

class TeamUserFormlet extends Formlet {
	public $formletView = "team.user";

	private $categoryController;

	public function __construct(CategoryController $categoryController) {
		$this->categoryController = $categoryController;
	}

	/**
	 * Prepare the form with fields
	 *
	 * @return void
	 */
	public function prepareForm() : void {
		$roles = $this->model->teamRoles->pluck('id')->all();
		$field = new Select('role[]',Role::options($this->categoryController->getIds("ROLES")));
		$field->setMultiple(true)->setValue($roles);
		$this->add($field);
	}

}
<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 13/04/2017 10:29
 */

namespace App\Http\Formlets;

use App\Http\Controllers\CategoryController;
use App\Models\Role;
use RS\Form\Fields\Hidden;
use RS\Form\Fields\Select;
use RS\Form\Formlet;

class TeamUserFormlet extends Formlet {
	public $formletView = "team.user";
	private $options = null;
	protected $subscriber = "role[]";

	public function __construct(CategoryController $categoryController) {
		$this->options = Role::options($categoryController->getIds("ROLES"));
	}
	public function prepareForm() : void {
		$field = new Select('user[]',$this->options);
		$field->setMultiple(true)->setValueType(AbstractField::TYPE_ARRAY);
		$this->add($field);

	}
}


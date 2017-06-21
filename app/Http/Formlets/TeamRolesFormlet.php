<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 13/04/2017 10:29
 */

namespace App\Http\Formlets;

use App\Http\Controllers\CategoryController;
use App\Models\Role;
use RS\Form\Fields\AbstractField;
use RS\Form\Fields\Select;
use RS\Form\Formlet;

class TeamRolesFormlet extends Formlet {
	public $formletView = "team.role";
	private $options = null;
	protected $subscriber = "role_id[]";

	public function __construct(CategoryController $categoryController) {
		$this->options = Role::options($categoryController->getIds("ROLES"));
	}

	public function prepareForm() : void {
		$field = new Select('role_id[]',$this->options);
		$field->setMultiple(true)->setValueType(AbstractField::TYPE_ARRAY)->setUnChecked([]);
		$this->add($field);

	}
}


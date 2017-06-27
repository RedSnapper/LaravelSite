<?php
/**
 * Part of site
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 03/05/2017 11:32
 */

namespace App\Http\Formlets;

use App\Http\Formlets\Helpers\CategoryHelper;
use RS\Form\Fields\Checkbox;
use RS\Form\Fields\Input;
use RS\Form\Fields\Radio;
use RS\Form\Formlet;

class RoleRecordFormlet extends Formlet {
	/**
	 * @var CategoryHelper
	 */
	private $categoryHelper;

	public function __construct(CategoryHelper $categoryHelper) {
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
		$this->categoryHelper->field($this,'ROLES');

		$field = new Checkbox('team_based',1,0);  //set checked here. also use a mutator or allow null
		$this->add($field->setDefault(false)->setLabel('Team-based'));

		$this->add($field);
	}

}


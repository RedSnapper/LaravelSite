<?php
/**
 * Part of site
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 03/05/2017 11:32
 */

namespace App\Http\Formlets;

use App\Http\Formlets\Helpers\CategoryHelper;
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

	public function prepareForm() {
		$field = new Input('text', 'name');
		$this->add(
			$field->setLabel('Name')->setRequired()
		);
		$this->categoryHelper->field($this,'ROLES');
		$field = (new Radio('team_based',[0=>"Global Purpose",1=>"For Teams"]))->setLabel("Type");
		$this->add($field);
	}

}
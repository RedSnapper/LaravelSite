<?php
/**
 * Part of site
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 11/05/2017 10:05
 */

namespace App\Http\Formlets;

use App\Http\Formlets\Helpers\CategoryHelper;
use App\Models\Tag;
use RS\Form\Fields\AbstractField;
use RS\Form\Fields\Checkbox;
use RS\Form\Fields\Input;
use RS\Form\Formlet;

class TagFormlet extends Formlet {
	public $formView = "tag.form";

	private $categoryHelper;

	public function __construct(Tag $tag,CategoryHelper $categoryHelper) {
		$this->setModel($tag);
		$this->categoryHelper = $categoryHelper;
	}

	public function prepareForm() : void {
		$field = new Input('text', 'name');
		$field->setLabel('Name')->setRequired();
		$this->add($field);

		$field = new Checkbox('moderated',1); //set checked here. also use a mutator or allow null.
		$field->setValueType(AbstractField::TYPE_INT)->setDefault(0); //because this is an integer field.
		$field->setLabel('Moderated');
		$this->add($field);

		$this->categoryHelper->field($this,'TAGS');
	}

	public function rules(): array {
		return [
			'name'        => 'required|max:255',
			'category_id' => 'required|category'
		];
	}


}

<?php

namespace App\Http\Formlets\Helpers;

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Collection;
use RS\Form\Fields\Select;
use RS\Form\Formlet;

/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 20/04/2017 08:43
 */
class CategoryHelper {
	/**
	 * @var CategoryController
	 */
	private $categoryController;

	public function __construct(CategoryController $categoryController) {
		$this->categoryController = $categoryController;
	}

	public function field(Formlet $form,string $section) {
		$category = $form->getData('category'); 			//from the url.
		if(is_null($category)) {
			$category = $form->getModel()->category_id; //from the model.
		}
		$categoryOptions = $this->categoryController->options($section);
		if($categoryOptions->has($category)) {
			$field = new Select('category_id', $this->categoryController->options($section));
			$form->add(
				$field->setLabel("Category")
					->setPlaceholder("Please select a category")
					->setDefault($category)
			);
		}
	}

	public function available(string $section) : Collection {
		return $this->categoryController->getIds($section);
	}
}
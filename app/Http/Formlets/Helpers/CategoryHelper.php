<?php

namespace App\Http\Formlets\Helpers;

use App\Http\Controllers\CategoryController;
use App\Models\Category;
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
		$model = $form->getModel();
		if(is_null($model->category_id) ){
			$category = $form->getData('category');
			$model->setAttribute('category_id',is_a($category,Category::class) ? $category->id : $category);
		}

		$categoryOptions = $this->categoryController->options($section);
		if($categoryOptions->has($model->category_id)) {
			$field = new Select('category_id', $categoryOptions);
			$form->add(
				$field->setLabel("Category")
					->setPlaceholder("Please select a category")
					->setDefault($model->category_id)
			);
		}
	}

	public function available(string $section) : Collection {
		return $this->categoryController->getIds($section);
	}
}
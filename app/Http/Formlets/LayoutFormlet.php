<?php

namespace App\Http\Formlets;

use App\Http\Controllers\CategoryController;
use RS\Form\Fields\Select;
use RS\Form\Formlet;
use RS\Form\Fields\Input;
use App\Models\Layout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class LayoutFormlet  extends Formlet {

	public $formView = "layout.form";
	/**
	 * @var CategoryController
	 */
	private $categoryController;

	public function __construct(Layout $layout,CategoryController $categoryController) {
		$this->setModel($layout);
		$this->categoryController = $categoryController;
	}

	public function prepareForm(){
		$field = new Input('text','name');
		$this->add(
			$field->setLabel('Name')
		);
		$field = new Select('category_id',$this->categoryController->options('LAYOUTS'));
		$this->add(
			$field->setLabel("Category")
				->setPlaceholder("Please select a category")
				->setDefault($this->getData('category'))
		);
		$this->addSubscribers('segments',LayoutSegmentFormlet::class,$this->model->segments());

	}

	public function rules():array{
		$key = $this->model->getKey();
		return [
			'name' => ['required','max:255',Rule::unique('layouts')->ignore($key)]
		];
	}

	public function edit(): Model {

		$layout = parent::edit();

		$layout->segments()->sync($this->getSubscriberFields('segments'));

		return $layout;
	}


	public function persist():Model {
		return $this->edit();
	}

}
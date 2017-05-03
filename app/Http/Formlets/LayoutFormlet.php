<?php

namespace App\Http\Formlets;

use App\Http\Controllers\CategoryController;
use App\Http\Formlets\Helpers\CategoryHelper;
use RS\Form\Fields\Select;
use RS\Form\Formlet;
use RS\Form\Fields\Input;
use App\Models\Layout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class LayoutFormlet  extends Formlet {

	/**
	 * @var CategoryHelper
	 */
	private $categoryHelper;

	public function __construct(Layout $layout,CategoryHelper $categoryHelper) {
		$this->setModel($layout);
		$this->categoryHelper = $categoryHelper;
	}

	public function prepareForm(){
		$field = new Input('text','name');
		$this->add($field->setLabel('Name'));
		$this->categoryHelper->field($this,'LAYOUTS');
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
<?php

namespace App\Http\Formlets;

use App\Http\Formlets\Helpers\CategoryHelper;
use RS\Form\Formlet;
use RS\Form\Fields\Input;
use App\Models\Layout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class LayoutFormlet  extends Formlet {
	public $formView = "layout.form";

	private $categoryHelper;

	public function __construct(Layout $layout,CategoryHelper $categoryHelper) {
		$this->setModel($layout);
		$this->categoryHelper = $categoryHelper;
	}

	public function prepareForm(){
		$field = new Input('text','name');
		$this->add($field->setLabel('Name')->setRequired());
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
		$segments = $this->getSubscriberFields('segments');
		$layout->segments()->sync($segments);
		return $layout;
	}


}
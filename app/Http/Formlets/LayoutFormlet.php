<?php

namespace App\Http\Formlets;

use RS\Form\Formlet;
use RS\Form\Fields\Input;
use App\Models\Layout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class LayoutFormlet  extends Formlet {

	public $formView = "layout.form";

	public function __construct(Layout $layout) {
		$this->setModel($layout);
	}

	public function prepareForm(){
		$field = new Input('text','name');
		$this->add(
			$field->setLabel('Name')
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
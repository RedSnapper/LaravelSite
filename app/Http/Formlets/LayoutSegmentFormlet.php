<?php

namespace App\Http\Formlets;

use RS\Form\Formlet;
use RS\Form\Fields\Checkbox;
use RS\Form\Fields\Input;
use Illuminate\Contracts\Validation\Validator;

class LayoutSegmentFormlet extends Formlet {

	public $formletView = "layout.segment";
	protected $subscriber = "subscriber";


	public function prepareForm() {
		$this->add((new Checkbox('subscriber')));

		$field = new Input('text', 'syntax');
		$field->setValue($this->getData('subscriber.pivot.syntax'));
		$field->setDefault($this->getData('option.syntax'));
		$this->add($field);
	}

	public function addCustomValidation(Validator $validator) {
		$validator->sometimes('syntax','required|max:50',function($input){
			return isset($input->subscriber);
		});
	}

}
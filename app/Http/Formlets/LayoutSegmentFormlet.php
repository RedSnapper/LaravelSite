<?php

namespace App\Http\Formlets;

use RS\Form\Formlet;
use RS\Form\Fields\Checkbox;
use RS\Form\Fields\Input;
use Illuminate\Contracts\Validation\Validator;

class LayoutSegmentFormlet extends Formlet {

	public $formletView = "layout.segment";

	public function prepareForm() {
		$this->add((new Checkbox('subscriber')));

		$field = new Input('text', 'syntax');
		$this->add(
		  $field->setDefault($this->getData('segments.syntax'))
		);
	}

	public function rules(): array {
		return[];
	}

	public function addCustomValidation(Validator $validator) {
		$validator->sometimes('syntax','required|max:50',function($input){
			return isset($input->subscriber);
		});
	}

}
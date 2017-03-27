<?php

namespace App\Http\Formlets;

use App\Http\Fields\Checkbox;
use App\Http\Fields\Input;
use Illuminate\Contracts\Validation\Validator;

class SegmentLayoutFormlet extends Formlet {

	public $formletView = "segment.layout";

	public function prepareForm() {
		$this->add((new Checkbox('subscriber')));
		$this->add((new Input('text', 'syntax')));
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
<?php

namespace App\Http\Formlets;

use App\Http\Fields\Checkbox;
use App\Http\Fields\Input;

class LayoutSegmentFormlet extends Formlet {

	public function prepareForm() {
		$this->add((new Input('text', 'syntax'))
		  ->setLabel('Syntax')
		);
		$this->add((new Checkbox(''))
		  ->setLabel('Name here')
		);
	}

	public function rules(): array {
		return[
		  'syntax' => 'required'
		];
	}

}
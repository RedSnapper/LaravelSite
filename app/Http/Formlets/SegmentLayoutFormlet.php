<?php

namespace App\Http\Formlets;

use App\Http\Fields\Checkbox;
use App\Http\Fields\Input;

class SegmentLayoutFormlet extends Formlet {

	public $formletView = "segment.layout";

	public function prepareForm() {
		$this->add((new Checkbox('subscriber')));
		$this->add((new Input('text', 'syntax',null,$this->getData('segment.syntax'))));
	}
	
}
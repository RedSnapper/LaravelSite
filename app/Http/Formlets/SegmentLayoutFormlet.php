<?php

namespace App\Http\Formlets;

use RS\Form\Formlet;
use RS\Form\Fields\Checkbox;
use RS\Form\Fields\Input;

class SegmentLayoutFormlet extends Formlet {

	public $formletView = "segment.layout";
	protected $subscriber = "subscriber";

	public function prepareForm() {
		$this->add((new Checkbox('subscriber')));
		$field = new Input('text', 'syntax');
		$this->add(
		  $field->setDefault($this->getData('segment.syntax'))
		);
	}

}
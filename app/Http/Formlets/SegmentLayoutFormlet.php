<?php

namespace App\Http\Formlets;

use RS\Form\Formlet;
use RS\Form\Fields\Checkbox;
use RS\Form\Fields\Input;

class SegmentLayoutFormlet extends Formlet {
	public $formletView = "segment.layout";
	protected $subscriber = "subscriber";

	public function prepareForm() : void {
		$this->add((new Checkbox('subscriber')));

		$field = new Input('text', 'syntax');
		$field->setValue($this->getData('subscriber.pivot.syntax'));
		$field->setDefault($this->getData('master.syntax'));
		$this->add($field);
	}

}
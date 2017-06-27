<?php

namespace App\Http\Formlets;

use RS\Form\Fields\AbstractField;
use RS\Form\Formlet;
use RS\Form\Fields\Checkbox;
use RS\Form\Fields\Input;
use Illuminate\Contracts\Validation\Validator;

class LayoutSegmentFormlet extends Formlet {

	public $formletView = "layout.segment";
	protected $subscriber = "subscriber";

	public function prepareForm() : void {
		$this->add((new Checkbox('subscriber',true,false))->setValueType(AbstractField::TYPE_BOOL));
		$this->add((new Input('text','local_name')));
		$this->add((new Input('text','tab')));
		$this->add((new Input('text','syntax'))->setDefault($this->getData('option.syntax')));
	}

	public function addCustomValidation(Validator $validator): void {
		$validator->sometimes('syntax','required|max:50',function($input){
			return isset($input->subscriber);
		});
	}

}

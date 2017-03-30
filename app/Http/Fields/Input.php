<?php

namespace App\Http\Fields;

class Input extends AbstractField {

	protected $view = "forms.fields.input";

	public function __construct(string $type, string $name) {
		$this->name = $name;
		$this->attributes = collect(['type'=>$type]);
	}




}
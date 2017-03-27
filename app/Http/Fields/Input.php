<?php

namespace App\Http\Fields;

class Input extends AbstractField {

	protected $view = "forms.fields.input";

	public function __construct(string $type, string $name, $value = null, $default=null) {
		$this->name = $name;
		$this->value = $value;
		$this->default = $default;
		$this->attributes = collect(['type'=>$type]);
	}




}
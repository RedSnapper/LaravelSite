<?php

namespace App\Http\Fields;

class Hidden extends AbstractField {
	protected $view = "forms.fields.hidden";

	public function __construct(string $name, $value = null) {
		$this->name = $name;
		$this->value = $value;
		$this->attributes = collect(['type'=>'hidden']);
	}

}
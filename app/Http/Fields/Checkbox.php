<?php

namespace App\Http\Fields;

class Checkbox extends AbstractField {

	protected $view = "forms.fields.checkbox";

	protected $type = "checkable";

	public function __construct(string $name, $value = 1,$checked=null) {
		$this->name = $name;
		$this->value = $value;
		$this->default = $checked;
		$this->attributes = collect([]);
	}

}
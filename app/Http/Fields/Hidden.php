<?php

namespace App\Http\Fields;

class Hidden extends AbstractField {
	protected $view = "forms.fields.hidden";

	public function __construct(string $name) {
		$this->name = $name;
		$this->attributes = collect(['type'=>'hidden']);
	}

}
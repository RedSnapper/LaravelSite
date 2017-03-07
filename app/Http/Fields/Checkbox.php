<?php
/**
 * Created by PhpStorm.
 * User: param
 * Date: 07/03/2017
 * Time: 10:58
 */

namespace App\Http\Fields;

class Checkbox extends AbstractField {

	protected $view = "fields.checkbox";

	protected $type = "checkable";

	public function __construct(string $name, $value = 1,$checked=null) {
		$this->name = $name;
		$this->value = $value;
		$this->default = $checked;
		$this->attributes = collect([]);
	}

}
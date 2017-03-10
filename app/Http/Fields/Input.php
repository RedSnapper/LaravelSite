<?php
/**
 * Created by PhpStorm.
 * User: param
 * Date: 01/03/2017
 * Time: 11:43
 */

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
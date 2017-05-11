<?php
/**
 * Part of site
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 11/05/2017 08:58
 */

namespace App\Http\Formlets\Helpers;

use RS\Form\Fields\Input;

class Rating extends Input {

	public function __construct($name) {
		//Note: Chrome does not handle fractional values with 'number' inputs.
		parent::__construct('number', $name);

		//These are set here for cross-site consistency.
		$this->setAttribute("class","hidden rating");
		$this->setAttribute("min","0");
		$this->setAttribute("max","5");
		$this->setAttribute("step","1");
//		$this->setAttribute("data-clear-value","0"); Doesn't seem to work.
		$this->setAttribute("data-animate","false");
		$this->setAttribute("data-show-caption","false");
		$this->setAttribute("data-size","sm");

	}
}

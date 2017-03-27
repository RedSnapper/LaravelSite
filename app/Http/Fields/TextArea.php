<?php
/**
 * Created by PhpStorm.
 * User: param
 * Date: 27/03/2017
 * Time: 15:36
 */

namespace App\Http\Fields;

class TextArea extends AbstractField {
	protected $view = "forms.fields.textarea";

	public function __construct(string $name, $value = null, $default=null) {
		$this->name = $name;
		$this->value = $value;
		$this->default = $default;
		$this->attributes = collect([]);
	}


	/**
	 * Set Number of rows
	 *
	 * @param int $rows
	 * @return AbstractField
	 */
	public function setRows(int $rows): AbstractField {
		$this->setAttribute('rows',$rows);
		return $this;
	}

	/**
	 * Set Number of cols
	 *
	 * @param int $cols
	 * @return AbstractField
	 */
	public function setCols(int $cols): AbstractField {
		$this->setAttribute('cols',$cols);
		return $this;
	}

}
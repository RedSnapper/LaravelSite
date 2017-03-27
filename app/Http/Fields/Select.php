<?php
/**
 * Created by PhpStorm.
 * User: param
 * Date: 27/03/2017
 * Time: 16:02
 */

namespace App\Http\Fields;

use Illuminate\Support\Collection;

class Select extends AbstractField {

	protected $view = "forms.fields.select";

	protected $list = [];

	/**
	 * @var Collection
	 */
	protected $options;

	public function __construct(string $name, $list =[], $selected = null) {
		$this->name = $name;
		$this->value = $selected;
		$this->attributes = collect([]);
		$this->list = $list;
		$this->setOptions();
	}

	/**
	 * Set placeholder
	 *
	 * @param string $string
	 * @return AbstractField
	 */
	public function setPlaceholder(string $string): AbstractField {

		$this->options->prepend(['display'=>$string,'value'=>null]);

		return $this;
	}

	protected function setOptions(){

		$this->options = collect($this->list)->map(function ($item,$key) {
			return [
			  'display' => $item,
			  'value'=> $key
			];
		})->values();

	}

	public function getData() {
		$data =  parent::getData();

		$data['options'] = $this->options;

		return $data;
	}

	/**
	 * Multi select
	 *
	 * @param boolean $multiple
	 * @return AbstractField
	 */
	public function setMultiple($multiple = true): AbstractField {

		$multiple ? $this->setAttribute('multiple')
		  : $this->removeAttribute("multiple");

		return $this;
	}

}
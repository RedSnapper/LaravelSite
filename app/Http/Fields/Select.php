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

	/**
	 * @var Collection
	 */
	protected $options;

	public function __construct(string $name, $list =[], $selected = null) {
		$this->name = $name;
		$this->value = $selected;
		$this->attributes = collect([]);
		$this->options = $this->setOptions($list);
	}

	/**
	 * Set placeholder. This option needs to be disabled.
	 *
	 * @param string $string
	 * @return AbstractField
	 */
	public function setPlaceholder(string $string): AbstractField {

		$this->options->prepend($this->option(null,$string,true));

		return $this;
	}

	protected function setOptions(array $list){

		return collect($list)->map(function ($item,$key) {

			if(!is_array($item)){
				return $this->option($key,$item);
			}

			return $this->optgroup($key,$item);


		})->values();

	}

	protected function option($value,$display,bool $disabled = false):\stdClass{

		$option = new \stdClass();
		$option->display = $display;
		$option->value = $value;
		$option->disabled = $disabled;
		return $option;
	}

	protected function optgroup($label,$options=[]):\stdClass{

		$group = new \stdClass();
		$group->label = $label;

		$group->options = $this->setOptions($options);

		return $group;
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
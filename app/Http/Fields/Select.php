<?php
/**
 * Created by PhpStorm.
 * User: param
 * Date: 27/03/2017
 * Time: 16:02
 */

namespace App\Http\Fields;

class Select extends AbstractField {

	protected $view = "forms.fields.select";

	protected $list = [];

	protected $options = [];

	public function __construct(string $name, $list =[], $selected = null) {
		$this->name = $name;
		$this->value = $selected;
		$this->attributes = collect([]);
		$this->list = $list;
		$this->setOptions();

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

}
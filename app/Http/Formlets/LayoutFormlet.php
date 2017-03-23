<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 23/03/2017 11:45
 */

namespace App\Http\Formlets;

use App\Http\Fields\Input;
use App\Layout;
use Illuminate\Validation\Rule;

class LayoutFormlet  extends Formlet {

	protected $formView = "layout.form";

	public function __construct(Layout $layout) {
		$this->setModel($layout);
	}

	public function prepareForm(){
		$field = new Input('text','name');
		$this->add(
			$field->setLabel('Name')->setRequired()
		);
	}

	public function rules():array{
		$key = $this->model->getKey();
		return [
			'name' => ['required','max:255',Rule::unique('layouts')->ignore($key)]
		];
	}

}
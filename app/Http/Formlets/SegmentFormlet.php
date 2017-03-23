<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 23/03/2017 11:52
 */

namespace App\Http\Formlets;

use App\Http\Fields\Input;
use App\Segment;
use Illuminate\Validation\Rule;

class SegmentFormlet extends Formlet {

	protected $formView = "segment.form";

	public function __construct(Segment $segment) {
		$this->setModel($segment);
	}

	public function prepareForm(){
		$this->add((new Input('text','name'))->setLabel('Name')->setRequired());
		$this->add((new Input('text','docs'))->setLabel('Docs')->setRequired());
	}

	public function rules():array{
		$key = $this->model->getKey();
		return [
			'name' => ['required','max:255',Rule::unique('segments')->ignore($key)]
		];
	}

}
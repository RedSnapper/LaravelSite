<?php

namespace App\Http\Formlets;

use App\Http\Fields\Input;
use App\Layout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class LayoutFormlet  extends Formlet {

	protected $formView = "layout.form";

	public function __construct(Layout $layout) {
		$this->setModel($layout);
	}

	public function prepareForm(){
		$field = new Input('text','name');
		$this->add(
			$field->setLabel('Name')
		);

		$this->addSubscribers('segments',LayoutSegmentFormlet::class,$this->model->segments());


	}

	public function rules():array{
		$key = $this->model->getKey();
		return [
			'name' => ['required','max:255',Rule::unique('layouts')->ignore($key)]
		];
	}

	public function edit(): Model {

		$segments = new Collection($this->fields('segments'));

		$segments = $segments->filter(function ($value, $key) {
			return isset($value['subscriber']);
		})->map(function ($item, $key) {

			array_forget($item,'subscriber');
			return $item;
		});

		$layout = parent::edit();

		$layout->segments()->sync($segments);

		return $layout;
	}


	public function persist():Model {
		return $this->edit();
	}

}
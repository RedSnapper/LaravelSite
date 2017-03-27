<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 23/03/2017 11:52
 */

namespace App\Http\Formlets;

use App\Http\Fields\Input;
use App\Segment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class SegmentFormlet extends Formlet {

	public $formView = "segment.form";

	public function __construct(Segment $segment) {
		$this->setModel($segment);
	}

	public function prepareForm(){
		$this->add((new Input('text','name'))->setLabel('Name')->setRequired());
		$this->add((new Input('text','docs'))->setLabel('Docs')->setRequired());

		$this->addSubscribers('layouts',SegmentLayoutFormlet::class,$this->model->layouts());

	}


	public function rules():array{
		$key = $this->model->getKey();
		return [
			'name' => ['required','max:255',Rule::unique('segments')->ignore($key)]
		];
	}

	public function edit(): Model {

		$layouts = new Collection($this->fields('layouts'));

		$layouts = $layouts->filter(function ($value, $key) {
			return isset($value['subscriber']);
		})->map(function ($item, $key) {

			array_forget($item,'subscriber');
			return $item;
		});

		$segment = parent::edit();

		$segment->layouts()->sync($layouts);

		return $segment;
	}


	public function persist():Model {
		return $this->edit();
	}

}
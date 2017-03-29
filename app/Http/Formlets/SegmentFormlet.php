<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 23/03/2017 11:52
 */

namespace App\Http\Formlets;

use App\Http\Fields\Input;
use App\Http\Fields\Radio;
use App\Http\Fields\Select;
use App\Http\Fields\TextArea;
use App\Models\Segment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class SegmentFormlet extends Formlet {

	public $formView = "segment.form";

	public function __construct(Segment $segment) {
		$this->setModel($segment);
	}

	public function prepareForm() {
		$this->add((new Input('text', 'name'))->setLabel('Name')->setRequired());
		$this->add((new Input('text', 'syntax'))->setLabel('Syntax'));
		$this->add((new TextArea('docs'))->setLabel('Docs')->setRows(3));

		$field = new Radio('size',['S'=>'Small','L'=>'Large'],'S');

		$this->add(
		  $field->setLabel("Size")
		);

		$this->addSubscribers('layouts', SegmentLayoutFormlet::class, $this->model->layouts());
	}

	/**
	 * Add subscribers to this formlet
	 *
	 * @param string        $name
	 * @param string        $class
	 * @param BelongsToMany $builder
	 */
	public function addSubscribers(string $name, string $class, BelongsToMany $builder) {

		$items = $builder->getRelated()->all();
		$models = $builder->get();

		foreach ($items as $item) {
			$formlet = app()->make($class);
			$formlet->with('segment', $this->model);
			$model = $this->getModelByKey($item->getKey(), $models);
			$this->addSubscriberFormlet($formlet, $name, $item, $model);
		}
	}

	public function rules(): array {
		$key = $this->model->getKey();
		return [
		  'name' => ['required', 'max:255', Rule::unique('segments')->ignore($key)]
		];
	}

	public function edit(): Model {

		$segment = parent::edit();

		$segment->layouts()->sync($this->getSubscriberFields('layouts'));

		return $segment;
	}

	public function persist(): Model {
		return $this->edit();
	}

}
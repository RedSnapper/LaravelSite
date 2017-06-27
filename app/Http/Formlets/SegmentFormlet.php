<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 23/03/2017 11:52
 */

namespace App\Http\Formlets;


use App\Http\Formlets\Helpers\CategoryHelper;
use RS\Form\Formlet;
use RS\Form\Fields\Input;

use RS\Form\Fields\TextArea;
use App\Models\Segment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\Rule;

class SegmentFormlet extends Formlet {
	public $formView = "segment.form";
	private $categoryHelper;

	public function __construct(Segment $segment,CategoryHelper $categoryHelper) {
		$this->setModel($segment);
		$this->categoryHelper = $categoryHelper;
	}

	public function prepareForm() : void {
		$this->add((new Input('text', 'name'))->setLabel('Name')->setRequired());
		$this->add((new Input('text', 'syntax'))->setLabel('Syntax'));
		$this->add((new TextArea('docs'))->setLabel('Docs')->setRows(3));
		$this->categoryHelper->field($this,'SEGMENTS');
		$this->addSubscribers('layouts', SegmentLayoutFormlet::class, $this->model->layouts());
	}

	public function rules(): array {
		$key = $this->model->getKey();
		return [
		  'name' => ['required', 'max:255', Rule::unique('segments')->ignore($key)],
		  'category_id' => 'required|category'
		];
	}

	public function edit(): Model {
		$segment = parent::edit();
		$this->subs($this->getFormlets('layouts'));
		return $segment;
	}

}
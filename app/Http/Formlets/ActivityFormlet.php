<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 05/04/2017 10:39
 */

namespace App\Http\Formlets;

use RS\Form\Formlet;
use RS\Form\Fields\Input;
use RS\Form\Fields\Select;
use RS\Form\Fields\TextArea;
use App\Models\Category;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\Rule;


class ActivityFormlet extends Formlet {
	public $formView = "activity.form";

	public function __construct(Activity $activity) {
		$this->setModel($activity);
	}

	public function prepareForm() {
		$this->add((new Input('text', 'name'))->setLabel('Name')->setRequired());
		$this->add((new Input('text', 'label'))->setLabel('Label'));
		$field = new Select('category_id',Category::options('ACTIVITIES'));
		$this->add(
			$field->setLabel("Category")
				->setPlaceholder("Please select a category")
				->setDefault($this->getData('category'))
		);

		$this->addSubscribers('roles', ActivityRoleFormlet::class, $this->model->roles());

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
			$formlet->with('activity', $this->model);
			$model = $this->getModelByKey($item->getKey(), $models);
			$this->addSubscriberFormlet($formlet, $name, $item, $model);
		}
	}

	public function rules(): array {
		$key = $this->model->getKey();
		return [
			'name' => ['required', 'max:255', Rule::unique('activities')->ignore($key)],
			'category_id' => 'required'
		];
	}

	public function edit(): Model {

		$activity = parent::edit();

		$activity->roles()->sync($this->getSubscriberFields('roles'));

		return $activity;
	}

	public function persist(): Model {
		return $this->edit();
	}
}
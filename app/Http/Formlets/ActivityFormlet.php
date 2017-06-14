<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 05/04/2017 10:39
 */

namespace App\Http\Formlets;

use App\Http\Formlets\Helpers\CategoryHelper;
use RS\Form\Fields\TextArea;
use RS\Form\Formlet;
use RS\Form\Fields\Input;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\Rule;


class ActivityFormlet extends Formlet {
	public $formView = "activity.form";
	/**
	 * @var CategoryHelper
	 */
	private $categoryHelper;

	public function __construct(Activity $activity,CategoryHelper $categoryHelper) {
		$this->setModel($activity);
		$this->categoryHelper = $categoryHelper;
	}

	public function prepareForm() : void {
		$this->add((new Input('text', 'name'))->setLabel('Name')->setRequired());
		$this->add((new Input('text', 'label'))->setLabel('Label'));
		$this->add((new TextArea('comment'))->setLabel('Comment'));
		$this->categoryHelper->field($this,'ACTIVITIES');
		$this->addSubscribers('roles', ActivityRoleFormlet::class, $this->model->roles(),$this->model->availableRoles());
	}

	public function rules(): array {
		$key = $this->model->getKey();
		return [
			'name' => ['required', 'max:255', Rule::unique('activities')->ignore($key)],
			'category_id' => 'required|category'
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
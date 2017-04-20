<?php

namespace App\Http\Formlets;

use App\Http\Controllers\CategoryController;
use App\Http\Formlets\Helpers\CategoryHelper;
use Illuminate\Database\Eloquent\Model;
use RS\Form\Formlet;
use RS\Form\Fields\Input;
use RS\Form\Fields\Select;
use App\Models\Role;
use App\Models\Category;

class RoleFormlet extends Formlet {

	public $formView = "role.form";
	/**
	 * @var CategoryHelper
	 */
	private $categoryHelper;

	public function __construct(Role $role,CategoryHelper $categoryHelper) {
		$this->setModel($role);
		$this->categoryHelper = $categoryHelper;
	}

	public function prepareForm() {
		$field = new Input('text', 'name');
		$this->add(
		  $field->setLabel('Name')->setRequired()
		);

		$this->categoryHelper->field($this,'ROLES');

		$this->addSubscribers('activities', RoleActivityFormlet::class, $this->model->activities());

		$this->addSubscribers('categories', RoleCategoryFormlet::class, $this->model->categories(),Category::ordered()->get());

	}

	public function rules(): array {
		return [
		  'name' => 'required|max:255',
		  'category_id' => 'required|category'
		];
	}

	public function edit(): Model {

		$role = parent::edit();

		$role->activities()->sync($this->getSubscriberFields('activities'));

		$role->categories()->sync($this->getSubscriberFields('categories'));

		return $role;
	}

	public function persist(): Model {
		return $this->edit();
	}

}
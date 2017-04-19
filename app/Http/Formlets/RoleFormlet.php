<?php

namespace App\Http\Formlets;

use Illuminate\Database\Eloquent\Model;
use RS\Form\Formlet;
use RS\Form\Fields\Input;
use RS\Form\Fields\Select;
use App\Models\Role;
use App\Models\Category;

class RoleFormlet extends Formlet {

	public $formView = "role.form";
	/**
	 * @var Category
	 */
	private $category;

	public function __construct(Role $role,Category $category) {
		$this->setModel($role);
		$this->category = $category;
	}

	public function prepareForm() {
		$field = new Input('text', 'name');
		$this->add(
		  $field->setLabel('Name')->setRequired()
		);

		$field = new Select('category_id', $this->category->options('ROLES'));
		$this->add(
		  $field->setLabel("Category")
			->setPlaceholder("Please select a category")
			->setDefault($this->getData('category'))
		);

		$this->addSubscribers('activities', RoleActivityFormlet::class, $this->model->activities());

		$this->addSubscribers('categories', RoleCategoryFormlet::class, $this->model->categories(),Category::ordered()->get());

	}

	public function rules(): array {
		return [
		  'name' => 'required|max:255',
		  'category_id' => 'required'
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
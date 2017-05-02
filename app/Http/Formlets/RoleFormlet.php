<?php

namespace App\Http\Formlets;

use App\Http\Formlets\Helpers\CategoryHelper;
use App\Models\Category;
use App\Models\Role;
use App\Policies\Helpers\UserPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use RS\Form\Fields\Input;
use RS\Form\Formlet;

//TODO: Actually USE the 'modify' vs 'view' values as stored in category_role.

class RoleFormlet extends Formlet {
	public $formView = "role.form";
	/**
	 * @var CategoryHelper
	 */
	private $categoryHelper;

	public function __construct(Role $role, CategoryHelper $categoryHelper) {
		$this->setModel($role);
		$this->categoryHelper = $categoryHelper;
	}

	public function prepareForm() {
		$field = new Input('text', 'name');
		$this->add(
			$field->setLabel('Name')->setRequired()
		);

		$this->categoryHelper->field($this, 'ROLES');

		$this->addSubscribers('activities', RoleActivityFormlet::class, $this->model->activities());

		$this->addSubscribers('categories', RoleCategoryFormlet::class, $this->model->categories()->withPivot('modify'), Category::ordered()->get());
	}

	public function rules(): array {
		return [
			'name'        => 'required|max:255',
			'category_id' => 'required|category'
		];
	}

	public function edit(): Model {

		$role = parent::edit();

		$role->activities()->sync($this->getSubscriberFields('activities'));

		$closure = function (Collection $collection): Collection {
			return $collection->filter(
				function ($array) {
					return ((int)$array['modify'] !== UserPolicy::INHERITING);
				}
			);
		};

		$catSubs = $this->getSubscriberFields('categories', 'modify', $closure);
		$role->categories()->sync($catSubs);
		return $role;
	}

	public function persist(): Model {
		return $this->edit();
	}
}
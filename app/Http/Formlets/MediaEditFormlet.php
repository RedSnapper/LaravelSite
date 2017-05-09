<?php

namespace App\Http\Formlets;

use App\Http\Controllers\CategoryController;
use App\Http\Formlets\Helpers\CategoryHelper;
use App\Models\Media;
use App\Models\Team;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use RS\Form\Fields\Input;
use RS\Form\Fields\Select;
use RS\Form\Formlet;

class MediaEditFormlet extends Formlet {
	public $formView = "media.edit";
	/**
	 * @var CategoryHelper
	 */
	private $categoryHelper;

	public function __construct(Media $media, CategoryHelper $categoryHelper) {
		$this->setModel($media);
		$this->categoryHelper = $categoryHelper;
	}

	/**
	 * Prepare the form with fields
	 *
	 * @return void
	 */
	public function prepareForm() {
		$field = new Input('text', 'name');
		$this->add($field->setLabel("Name")->setRequired(true));

		$this->categoryHelper->field($this,'MEDIA');
		$field = new Select('team_id', auth()->user()->teams()->pluck('name', 'id'));
		$this->add(
			$field->setLabel("Team")
				->setPlaceholder("Please select a team")
		);

		$field = new Input('file', 'media');
		$this->add($field->setLabel("Media"));

		$field = new Input('text', 'filename');
		$this->add($field->setLabel("Filename"));
	}

	public function edit(): Model {
		return $this->model->saveMedia($this->fields(), $this->request->file('media'));
	}

	protected function undo(): bool {
		$this->model->undo();
		return false; //don't process as a form.
	}

	protected function redo(): bool {
		$this->model->redo();
		return false; //don't process as a form.
	}

	public function rules(): array {
		$key = $this->model->getKey();

		$rules = [
			'media'       => ['image'],
			'name'        => ['required', 'max:255', Rule::unique('media')->ignore($key)],
			'filename'    => ['required', 'max:255'],
			'category_id' => 'required|category',
			'team_id'     => 'required|integer',

		];

		return $rules;
	}
}

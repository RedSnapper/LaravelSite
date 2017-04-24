<?php

namespace App\Http\Formlets;

use App\Http\Controllers\CategoryController;
use App\Http\Formlets\Helpers\CategoryHelper;
use App\Models\Media;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use RS\Form\Fields\Input;
use RS\Form\Fields\Select;
use RS\Form\Formlet;

class MediaFormlet extends Formlet {

	public $formView = "media.form";
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

		$this->categoryHelper->field($this, 'MEDIA');

		$field = new Select('team_id', auth()->user()->teams()->pluck('name', 'id'));
		$this->add(
		  $field->setLabel("Team")
			->setPlaceholder("Please select a team")
		  	//->setDefault($this->getData('team')->id)
		);

		$field = new Input('file', 'media');
		$this->add($field->setLabel("Media"));
	}

	public function persist(): Model {
		return $this->model->saveMedia($this->fields(), $this->request->file('media'));
	}

	public function rules(): array {

		return [
		  'media'       => 'required|image',
		  'name'        => 'required|max:255|unique:media',
		  'category_id' => 'required|category',
		  'team_id'     => 'required|exists:teams,id'
		];
	}

}

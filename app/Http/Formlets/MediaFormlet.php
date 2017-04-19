<?php

namespace App\Http\Formlets;

use App\Models\Category;
use App\Models\Media;
use App\Models\Team;
use Illuminate\Database\Eloquent\Model;
use RS\Form\Fields\Input;
use RS\Form\Fields\Select;
use RS\Form\Formlet;

class MediaFormlet extends Formlet {

	public $formView = "media.form";
	/**
	 * @var Category
	 */
	private $category;

	public function __construct(Media $media,Category $category) {
		$this->setModel($media);
		$this->category = $category;
	}

	/**
	 * Prepare the form with fields
	 *
	 * @return void
	 */
	public function prepareForm() {

		$field = new Input('text', 'name');
		$this->add($field->setLabel("Name")->setRequired(true));

		$field = new Select('category_id',$this->category->options('MEDIA'));
		$this->add(
		  $field->setLabel("Category")
			->setPlaceholder("Please select a category")
			->setDefault($this->getData('category'))
		);

		$field = new Select('team_id',Team::options());
		$this->add(
			$field->setLabel("Team")
				->setPlaceholder("Please select a team")
//				->setDefault($this->getData('team'))
		);

		$field = new Input('file', 'media');
		$this->add($field->setLabel("Media"));

	}

	public function persist(): Model {
		return $this->model->saveMedia($this->fields(),$this->request->file('media'));
	}

	public function rules():array{

		return [
		  'media' => 'required|image',
		  'name' => 'required|max:255|unique:media',
		  'category_id' => 'required|exists:categories,id',
		  'team_id' => 'required|exists:teams,id'
		];
	}

}

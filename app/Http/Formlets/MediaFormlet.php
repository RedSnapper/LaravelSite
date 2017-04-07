<?php

namespace App\Http\Formlets;

use App\Models\Category;
use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use RS\Form\Fields\Input;
use RS\Form\Formlet;

class MediaFormlet extends Formlet {

	public $formView = "media.form";

	public function __construct(Media $media) {
		$this->setModel($media);
	}

	/**
	 * Prepare the form with fields
	 *
	 * @return void
	 */
	public function prepareForm() {

		$field = new Input('text', 'name');
		$this->add($field->setLabel("Name")->setRequired(true));

		$field = new Input('file', 'media');
		$this->add($field->setLabel("Media")->setRequired(true));

	}

	public function persist(): Model {

		$file = $this->request->file('media');

		$path = $file->store('/media');

		$media = $this->model;
		$media->name = $this->fields('name');
		$media->path = $path;
		$media->mime = $file->getMimeType();
		$media->size = $file->getSize();

		$media->save();

		return $this->model;

	}

	public function rules(): array {
		return [
		  'media' => 'required|mimes:jpeg,bmp,png',
		  'name' => 'required'
		];
	}

}

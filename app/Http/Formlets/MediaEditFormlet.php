<?php

namespace App\Http\Formlets;

use App\Models\Category;
use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use RS\Form\Fields\Input;
use RS\Form\Fields\Select;
use RS\Form\Formlet;

class MediaEditFormlet extends Formlet{

	public $formView = "media.edit";

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

	   $field = new Select('category_id',Category::options('MEDIA'));
	   $this->add(
		 $field->setLabel("Category")
		   	->setPlaceholder("Please select a category")
	   		->setDefault($this->getData('category'))
	   );

	   $field = new Input('file', 'media');
	   $this->add($field->setLabel("Media"));

	   $field = new Input('text', 'filename');
	   $this->add($field->setLabel("Filename"));
   }

	public function edit(): Model {
		return $this->model->saveMedia($this->fields(),$this->request->file('media'));;
	}

	public function rules():array{
		$key = $this->model->getKey();

		$rules = [
		  'media' => ['image'],
		  'name' => ['required','max:255',Rule::unique('media')->ignore($key)],
		  'filename' => ['required','max:255'],
		  'category_id' => 'required|exists:categories,id'
		];

		return $rules;
	}


}

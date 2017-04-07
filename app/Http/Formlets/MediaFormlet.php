<?php

namespace App\Http\Formlets;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use RS\Form\Fields\Input;
use RS\Form\Formlet;

class MediaFormlet extends Formlet{

   public $formView = "media.form";

   /**
    * Prepare the form with fields
    *
    * @return void
    */
   public function prepareForm() {
   		$field= new Input('file','media');
		$this->add($field->setLabel("Media"));
   }

	public function persist(): Model {

   		$file = $this->request->file('media');
		$path = $file->store('media');

		return new Category();
	}

	public function rules(): array {
		return [
		  'media'=> 'required|mimes:jpeg,bmp,png'
		];
	}

}

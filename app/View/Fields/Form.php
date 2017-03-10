<?php

namespace App\View\Fields;

use RS\NView\Document;
use RS\NView\ViewController;

class Form extends ViewController{

   	public function render(Document $view, array $data): Document {


		foreach ($data['hidden'] as $fieldData){
			$field = view($fieldData['view'],$fieldData);
			$view->set("/*/child-gap()",$field->compile());
		}

		if(isset($data['attributes'])){
			foreach ($data['attributes'] as $attribute => $value){
				$view->set("/*/@$attribute",$value);
			}
		}

   		return $view;
   	}



}
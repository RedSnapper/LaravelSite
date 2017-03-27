<?php

namespace App\View\Forms\Fields;

use RS\NView\Document;
use RS\NView\ViewController;

class Input extends Field {


	public function render(Document $view, array $data): Document {

		$this->renderDefaults($view,$data);
		return $view;
	}



}
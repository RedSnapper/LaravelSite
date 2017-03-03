<?php

namespace App\View\Fields;

use RS\NView\Document;

class Hidden extends Input {


	public function render(Document $view, array $data): Document {

		$this->renderAttributes($view,$data);
		$this->renderValue($view,$data);
		$this->renderName($view,$data);

		return $view;
	}

}
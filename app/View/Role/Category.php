<?php

namespace App\View\Role;

use RS\Form\View\Main;
use RS\NView\Document;

class Category extends Main {

	public function render(Document $view,array $data): Document {
		parent::render($view,$data);
		$depth = $data['model']->depth ?? 0;
		$view->set("//*[@data-xp='indent']/@class","tier-" . $depth);
		return $view;
	}

}
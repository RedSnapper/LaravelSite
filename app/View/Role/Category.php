<?php

namespace App\View\Role;

use App\View\Form\Main;
use RS\NView\Document;

class Category extends Main {

	public function render(Document $view,array $data): Document {
		parent::render($view,$data);
		$depth = $data['categories']->depth ?? 0;
		$view->set("//*[@data-xp='indent']/@class","tier-" . $depth);
		return $view;
	}

}
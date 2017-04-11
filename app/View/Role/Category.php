<?php

namespace App\View\Role;

use App\View\Form\Main;
use RS\NView\Document;
//use RS\NView\ViewController;

class Category extends Main {
//data-v.controller="form.main"
	public function render(Document $view,array $data): Document {
		parent::render($view,$data);
		$depth = $data['categories']->depth ?? 0;
		$view->set("//*[@data-xp='indent']/@class","tier-" . $depth);
		return $view;
	}

}
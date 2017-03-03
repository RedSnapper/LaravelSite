<?php

namespace App\View\Pages;

use RS\NView\Document;
use RS\NView\ViewController;

class Home extends ViewController {

	//protected $parent = "pages.main";

	public function render(Document $view,array $data): Document {

		$view->set("//*[@data-xp='time']", (microtime(true) - LARAVEL_START));
		return $view;
	}

}
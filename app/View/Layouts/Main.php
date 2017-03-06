<?php

namespace App\View\Layouts;

use RS\NView\Document;
use RS\NView\View;
use RS\NView\ViewController;

class Main extends ViewController {

	//protected $parent = "page";

	public function compose(View $view) {
		$view->with('foo','bar');
	}

	public function renderChild(Document $view, Document $child,array $data): Document {
		$view->set('//*[@data-v.section="main"]',$child);
		return $view;
	}

}
<?php

namespace App\View\Layouts;
use App\Models\Category;
use RS\NView\View;
use RS\NView\ViewController;

class Integrity extends ViewController {
	private $category;
	function __construct(Category $category) {
		$this->category = $category;
	}

	public function compose(View $view) {
		$integrity = $this->category->checkIntegrity();
		$view->with("integrity",$integrity);
	}

}



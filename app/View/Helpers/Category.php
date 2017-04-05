<?php

namespace App\View\Helpers;

use RS\NView\Document;
use RS\NView\ViewController;

class Category extends ViewController {

	public function render(Document $view,array $data): Document {

		$category = @$data['category'];

		if($category){
			$view->set("//*[@data-v.xp='tree']/@data-selected",$category->id);
		}

		return $view;
	}

}
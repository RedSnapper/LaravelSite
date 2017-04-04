<?php

namespace App\View\Segment;

use RS\NView\Document;
use RS\NView\ViewController;

class Index extends ViewController {


	public function render(Document $view,array $data): Document {

		$category = $data['category'];

		if($category){
			$view->set("//*[@data-v.xp='tree']/@data-selected",$category->id);
		}



		return $view;
	}

}
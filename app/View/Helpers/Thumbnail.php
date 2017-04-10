<?php

namespace App\View\Helpers;

use RS\NView\Document;
use RS\NView\ViewController;

class Thumbnail extends ViewController {

	public function render(Document $view,array $data): Document {

		$media = @$data['media'];

		if($media){
			$view->set("./@src",$media->getThumbnailPath());
		}

		return $view;
	}

}
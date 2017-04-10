<?php

namespace App\View\Media;

use RS\NView\Document;
use RS\NView\ViewController;

class Edit extends ViewController {

	public function render(Document $view, array $data): Document {

		$media = @$data['media'];

		if($media){
			$view->set("//*[@data-v.xp='fullsize']/@href",$media->getPath());
		}

		return $view;
	}

}
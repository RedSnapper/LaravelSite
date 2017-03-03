<?php

namespace App\View\Layouts;

use RS\NView\Document;
use RS\NView\ViewController;

class Well extends ViewController{

   	public function renderChild(Document $view, Document $child, array $data): Document {
   		$view->set('//*[@data-v.section="main"]',$child);
		//$view->set('//*/@data-v.section');

		return $view;
   	}
}
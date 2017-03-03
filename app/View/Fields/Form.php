<?php

namespace App\View\Fields;

use RS\NView\Document;
use RS\NView\ViewController;

class Form extends ViewController{



   	public function renderChild(Document $view, Document $child, array $data): Document {

   		$view->set("/*/child-gap()",$child);

   		return $view;
   	}
}
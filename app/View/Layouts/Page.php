<?php

namespace App\View\Layouts;

use RS\NView\Document;
use RS\NView\ViewController;


class Page extends ViewController {

	public function render(Document $view,array $data): Document {

		if(isset($data['title'])){
			$view->set("//h:title/text()","Builder | {$data['title']}");
		}

		$view->set("//h:meta[@name='csrf-token']/@content",csrf_token());

		$view->set("/h:html/@lang",config('app.locale'));

		return $view;
	}

	public function renderChild(Document $view, Document $child, array $data): Document {
		$view->set('//*[@data-v.section="main"]',$child);
		return $view;
	}

}
<?php

namespace App\View\Forms\Fields;

use RS\NView\Document;
use RS\NView\ViewController;

class Input extends ViewController {


	public function render(Document $view, array $data): Document {

		$this->renderLabel($view,$data);
		$this->renderAttributes($view,$data);
		$this->renderValue($view,$data);
		$this->renderName($view,$data);
		$this->renderID($view,$data);
		$this->renderError($view,$data);

		return $view;
	}


	protected function renderLabel(Document $view,array $data) {
		if (!isset($data['label'])){
			$view->set("//*[@data-v.xp='label']");
		}else{
			$view->set("//*[@data-v.xp='label']/@for",$data['name']);
		}
	}

	protected function renderAttributes(Document $view, $data) {
		if(isset($data['attributes'])){
			foreach ($data['attributes'] as $attribute => $value){
				$view->set("//h:input/@$attribute",$value);
			}
		}
	}

	protected function renderValue(Document $view, $data) {
		if(!is_null(@$data['value'])){
			$view->set("//h:input/@value",e($data['value']));
		}
	}

	protected function renderName(Document $view, $data) {
		$view->set("//h:input/@name",$data['name']);
	}

	protected function renderID(Document $view, $data){
		$view->set("//h:input/@id",$data['name']);
	}

	protected function renderError(Document $view, $data) {
		$errors = $data['errors'];
		if(count($errors)){
			$view->set("/*/@class/child-gap()"," has-error");
		}

	}

}
<?php

namespace App\View\Helpers;

use RS\NView\Document;
use RS\NView\View;
use RS\NView\ViewController;

class Complex extends ViewController {
	/**
	 * @var View
	 */
	private $view;
	private $simple;
	private $complex;

	public function compose(View $view) {
		parent::compose($view);
		$this->view = $view;
	}

	public function render(Document $view,array $data = null): Document {
		if(is_null($data)) {
			$view->set("/*");
			return $view;
		}
		$this->simple = $view->consume("//*[@data-v.simple]");   //has key and value
		$this->complex = $view->consume("//*[@data-v.complex]"); //has a tag marked as inner
		$this->outer = $view->consume("//*[@data-v.outer]");     //has title/container
		return $this->recurse("",$data);
	}

	private function sanitise(string $stuff) : string {
		return preg_replace_callback('/([\x00-\x1F\x7F]+)/u',
			function ($matches) {
			return "0x" . bin2hex($matches[0]);
		},$stuff);
	}

	private function recurse(string $name,array $data) : Document {
		if(count($data) == 0) {
			return new Document("<span />");
		}
		$prime = new Document($this->outer);
		if($name != "") {
			$prime->set("//*[@data-v.title]",$this->sanitise($name));
		} else {
			$prime->set("//*[@data-v.title-holder]");
		}
		foreach($data as $key => $value) {
			if(is_array($value)) {
				$container = new Document($this->complex);
				$container->set("//*[@data-v.inner]",$this->recurse($key,$value));
			} else {
				$container = new Document($this->simple);
				$container->set("//*[@data-v.key]",$this->sanitise($key));
				$container->set("//*[@data-v.value]",$this->sanitise($value));
			}
			$prime->set("//*[@data-v.container]/child-gap()",$container);
		}
		$prime->set("//*[@data-v.container]/@data-v.container");
		return $prime;
	}

}

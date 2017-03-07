<?php

namespace App\View\Layouts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use RS\NView\Document;
use RS\NView\ViewController;

class Pagination extends ViewController {

	public function render(Document $view, array $data): Document {

		$paginator = $data['paginator'];

		$this->renderElements($view,$paginator);

		$this->renderPrevious($view, $paginator);

		$this->renderNext($view, $paginator);

		return $view;
	}

	protected function renderPrevious(Document $view, LengthAwarePaginator $paginator) {

		if ($paginator->onFirstPage()) {
			$view->set('//*[@data-v.xp="prevEnabled"]');
		} else {
			$view->set('//*[@data-v.xp="prevDisabled"]');
			$view->set("//*[@data-v.xp='prevLink']/@href", $paginator->previousPageUrl());
		}
	}

	protected function renderNext(Document $view, LengthAwarePaginator $paginator) {

		if ($paginator->hasMorePages()) {
			$view->set('//*[@data-v.xp="nextDisabled"]');

			$url = $paginator->nextPageUrl();
			$next = view($view->consume('//*[@data-v.xp="nextEnabled"]'),compact('url'));

		} else {
			$view->set('//*[@data-v.xp="nextEnabled"]');
			$next = view($view->consume('//*[@data-v.xp="nextDisabled"]'));

		}

		$view->set("//h:ul/child-gap()",$next);
	}

	protected function renderElements(Document $view, LengthAwarePaginator $paginator){

		$elements = $this->elements($paginator);

		$currentPage = $view->consume('//*[@data-v.xp="currentPage"]');
		$pageTemplate = $view->consume('//*[@data-v.xp="page"]');
		$dots = $view->consume('//*[@data-v.xp="dots"]');

		foreach ($elements as $element) {

			if(is_string($element)){

				$child = view($dots,['page'=>$element]);
				$view->set("//h:ul/child-gap()",$child);
			}

			if(is_array($element)){
				foreach ($element as $page=>$url){
					if($page == $paginator->currentPage()){
						$child = view($currentPage,compact('page'));
					}else{
						$child = view($pageTemplate,compact('page','url'));
					}
					$view->set("//h:ul/child-gap()",$child);
				}
			}
		}


	}

	protected function elements(LengthAwarePaginator $paginator){
		$window = UrlWindow::make($paginator);

		return array_filter([
		  $window['first'],
		  is_array($window['slider']) ? '...' : null,
		  $window['slider'],
		  is_array($window['last']) ? '...' : null,
		  $window['last'],
		]);
	}

}
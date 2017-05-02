<?php
/**
 * Part of site
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 02/05/2017 14:59
 */

namespace App\View\Layouts;


use Illuminate\Support\Facades\Auth;
use RS\NView\Document;
use RS\NView\View;
use RS\NView\ViewController;

class MediaNav extends ViewController {

	public function compose(View $view) {

		if (!Auth::check()) {
			return;
		}

		$view->with('teams', Auth::user()->teams()->get());

	}

	public function render(Document $view, array $data): Document {
		if($data['teams']->count() == 0) {
			return new Document();
		}
		return $view;
	}

}
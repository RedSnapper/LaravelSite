<?php

namespace App\View\Helpers;

use Illuminate\Database\Eloquent\Model;
use RS\NView\Document;
use RS\NView\ViewController;

class Category extends ViewController {

	public function render(Document $view,array $data): Document {

		$category = @$data['category'];

		if($category){
			$id = is_a($category,\Closure::class) ? $category() : is_a($category,Model::class) ? $category->id : $category;
			$view->set("//*[@data-v.xp='tree']/@data-selected",$id);
		}

		return $view;
	}

}
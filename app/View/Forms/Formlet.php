<?php
/**
 * Created by PhpStorm.
 * User: param
 * Date: 28/02/2017
 * Time: 17:39
 */

namespace App\View\Forms;

use RS\NView\Document;
use RS\NView\ViewController;

class Formlet extends ViewController {
	public function render(Document $view,array $data): Document {

		foreach ($data['fields'] as $fieldData) {
			$name = $fieldData['name'];
			$fieldData['errors'] = $data['errors']->get($name);
			$field = view($fieldData['view'],$fieldData);

			$view->set("//*[@data-v.field='$name']",$field);
		}

		return $view;
	}


}
<?php
/**
 * Created by PhpStorm.
 * User: param
 * Date: 27/03/2017
 * Time: 16:23
 */

namespace App\View\Forms\Fields;

use RS\NView\Document;

class Select extends Field {

	protected $rootElement = "select";

	public function render(Document $view, array $data): Document {

		$this->renderDefaults($view,$data);

		$this->renderOptions($view, $data);

		return $view;
	}

	public function renderOptions(Document $view, array $data) {
		$optionTemplate = $view->consume("//h:option");

		$options = $data['options'];
		$selected =  $data['value'];

		foreach ($options as $option) {

			$optionView = new Document($optionTemplate);

			$optionView->set("//h:option/@value", $option['value']);
			$optionView->set("//h:option/child-gap()", $option['display']);

			if($this->isSelected($option['value'], $selected)){
				$optionView->set("//h:option/@selected", "selected");
			}

			$view->set("//h:select/child-gap()", $optionView);
		}
	}



	/**
	 * Determine if the value is selected.
	 *
	 * @param  string $value
	 * @param  string $selected
	 * @return bool
	 */
	protected function isSelected($value, $selected):bool {
		if (is_array($selected)) {
			return in_array($value, $selected);
		}
		return ((string)$value == (string)$selected);
	}

}
<?php
namespace App\Http\Formlets;

use App\Address;
use App\Http\Fields\Input;

class AddressFormlet extends Formlet {
	protected $formView = "user.form";

	public function __construct(Address $address) {
		$this->setModel($address);
	}

	public function prepareForm() {
		$field = new Input('text', 'street');
		$this->add($field->setLabel('Street'));
		$field = new Input('text', 'city');
		$this->add($field->setLabel('City'));
		$field = new Input('text', 'postcode');
		$this->add($field->setLabel('Postcode'));
	}

	public function rules(): array {
		return [
			'street'   => 'required|max:255',
			'city'     => 'required|max:255',
			'postcode' => 'required|max:32',
		];
	}
}


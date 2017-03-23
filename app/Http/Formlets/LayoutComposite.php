<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 23/03/2017 12:15
 */

namespace App\Http\Formlets;
use Illuminate\Database\Eloquent\Model;

class LayoutComposite extends Formlet {

	protected $view = "layout.composite";
	protected $formView = "layout.form";

	public function prepareForm(){
		$layout = $this->addFormlet('layout',LayoutFormlet::class)
			->setKey($this->key);
		$this->addFormlet('segments',Subscriber::class)
			->setModel($layout->getModel());
	}

	//update
	public function edit(): Model {
		$layout = $this->getFormlet('layout')->edit();
		$this->getFormlet('segments')->edit();
		return $layout;
	}

	//new
	public function persist():Model {
		$layout = $this->getFormlet('layout')->persist();
		$this->getFormlet('segments')->setModel($layout)->persist();
		return $layout;
	}

}

//public function __construct(Layout $layout) {
//	$this->setModel($layout);
//}
//
//public function prepareForm(){
//	$field = new Input('text','name');
//	$this->add(
//		$field->setLabel('Name')->setRequired()
//	);
//}
//
//public function rules():array{
//	$key = $this->model->getKey();
//	return [
//		'name' => ['required','max:255',Rule::unique('layouts')->ignore($key)]
//	];
//}
//

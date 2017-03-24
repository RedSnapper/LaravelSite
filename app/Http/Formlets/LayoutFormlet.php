<?php

namespace App\Http\Formlets;

use App\Http\Fields\Input;
use App\Layout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class LayoutFormlet  extends Formlet {

	protected $formView = "layout.form";

	//protected $compositeView = "layout.composite";

	public function __construct(Layout $layout) {
		$this->setModel($layout);
	}

	public function prepareForm(){
		$field = new Input('text','name');
		$this->add(
			$field->setLabel('Name')->setRequired()
		);

		$this->addSubscribers('segments',LayoutSegmentFormlet::class,$this->model->mm()->getRelated()->all(),$this->model->mm);

		//$this->addFormlets('segments',LayoutSegmentFormlet::class,$this->model->mm);

		//$this->addFormlet('segments',Subscriber::class)->setModel($this->getModel());


	}

	public function rules():array{
		$key = $this->model->getKey();
		return [
			'name' => ['required','max:255',Rule::unique('layouts')->ignore($key)]
		];
	}

	public function edit(): Model {
		dd($this->request->all());
		//$layout = parent::edit();
		//$this->getFormlet('segments')->setModel($layout)->persist();
		//return $layout;
	}


	public function persist():Model {
		$layout = parent::edit();
		$this->getFormlet('segments')->setModel($layout)->persist();
		return $layout;
	}

}
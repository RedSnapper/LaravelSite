<?php

namespace App\Http\Formlets;

use App\Http\Formlets\Helpers\CategoryHelper;
use RS\Form\Fields\Checkbox;
use RS\Form\Fields\Select;
use RS\Form\Formlet;
use RS\Form\Fields\Input;
use App\Models\Layout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class LayoutFormlet  extends Formlet {
	public $formView = "layout.form";

	private $categoryHelper;

	public function __construct(Layout $layout,CategoryHelper $categoryHelper) {
		$this->setModel($layout);
		$this->categoryHelper = $categoryHelper;
	}

	public function prepareForm(){
		$field = new Input('text','name');
		$this->add($field->setLabel('Name')->setRequired());

		$field = new Checkbox('build_point',1,0); //set checked here. also use a mutator or allow null
		$this->add($field->setDefault(true)->setLabel('Build point'));

		$field = new Checkbox('searchable',1,0);  //set checked here. also use a mutator or allow null
		$this->add($field->setDefault(false)->setLabel('Searchable'));

		$this->categoryHelper->field($this,'LAYOUTS');
		$this->addSubscribers('segments',LayoutSegmentFormlet::class,$this->model->segments());

		$options = Layout::options();
		$field = new Select('default_child',$options);
		$this->add($field->setLabel("Default Child"));

		//$table->integer('icon')->unsigned()->nullable(); //Need to add the icon table.

	}

	public function rules():array{
		$key = $this->model->getKey();
		return [
			'name' => ['required','max:255',Rule::unique('layouts')->ignore($key)]
		];
	}

	public function edit(): Model {
		$layout = parent::edit();
		$segments = $this->getSubscriberFields('segments');
		$layout->segments()->sync($segments);
		return $layout;
	}
//$speakers  = (array) Input::get('speakers'); // related ids
//$pivotData = array_fill(0, count($speakers), ['is_speaker' => true]);
//$syncData  = array_combine($speakers, $pivotData);
//
//$user->roles()->sync($syncData);

}
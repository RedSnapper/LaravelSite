<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 22/03/2017 10:53
 */

namespace App\Http\Formlets;

use Illuminate\Database\Eloquent\Relations\Relation;
use RS\Form\Formlet;
use Illuminate\Database\Eloquent\Model;
use RS\Form\Fields\Checkbox;

class Subscriber extends Formlet {

	protected $master;

	public function prepareForm(){
		throw new \Exception("This is no longer supported. Have a look at UserRoleFormlet");
		//$this->master  = $this->model;
		//$items = ($this->relation)->getRelated()->all();
		//foreach ($items as $item) {
		//	$this->add((new Checkbox('',$item->id))
		//		->setLabel($item->name)
		//	);
		//}
		//$this->model = ($this->relation)->get();
	}

	public function edit() : Model {
		//($this->relation)->sync($this->fields());
		//return $this->master;
	}

	public function persist(): Model {
		//($this->relation)->sync($this->fields());
		//return $this->model;
	}


}
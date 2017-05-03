<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 22/03/2017 10:53
 */

namespace App\Http\Formlets;

use RS\Form\Formlet;
use RS\Form\Fields\Checkbox;
use Illuminate\Database\Eloquent\Model;

class UserRoleFormlet extends Formlet {

	public function prepareForm(){
		$items = $this->model->roles()->getRelated()->all();
		foreach ($items as $item) {
			$this->add((new Checkbox('',$item->id))
				->setLabel($item->name)
			);
		}
	}

	public function edit() : Model {
		$this->model->roles()->sync($this->fields());
		return $this->model;
	}

	public function persist(): Model {
		$this->model->roles()->sync($this->fields());
		return $this->model;
	}


}
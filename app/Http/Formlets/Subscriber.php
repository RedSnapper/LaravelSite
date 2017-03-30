<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 22/03/2017 10:53
 */

namespace App\Http\Formlets;

use RS\Form\Formlet;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use RS\Form\Fields\Checkbox;

class Subscriber extends Formlet {
	/**
	 * @var Role
	 * Any hasMany table that wants to use this must have a reciprocal mm() method.
	 */
	protected $master;

	public function prepareForm(){
		$this->master  = $this->model;
		$items = $this->master->mm()->getRelated()->all();
		foreach ($items as $item) {
			$this->add((new Checkbox('',$item->id))
				->setLabel($item->name)
			);
		}
		$this->model  = $this->master->mm()->get();
	}

	public function edit() : Model {
		$this->master->mm()->sync($this->fields());
		return $this->master;
	}

	public function persist(): Model {
		$this->model->mm()->sync($this->fields());
		return $this->model;
	}


}
<?php
namespace App\Validators;
use App\Models\Category;
use Illuminate\Support\Facades\Gate;

/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 19/04/2017 11:13
 */
class CategoryValidator {

	public function validate($attribute, $value, $parameters, $validator) {
		$category = Category::find($value);
		if(is_null($category)) { return false; }
		return Gate::allows('view',$category);
	}
}
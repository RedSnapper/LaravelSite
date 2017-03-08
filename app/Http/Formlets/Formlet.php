<?php

namespace App\Http\Formlets;

use App\Http\Fields\AbstractField;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class Formlet {

	use ValidatesRequests;

	protected $view;

	/**
	 * All fields that are added.
	 *
	 * @var AbstractField[]
	 */
	protected $fields = [];

	/**
	 * All fields that are added.
	 *
	 * @var Session
	 */
	protected $session;

	/**
	 * @var Request
	 */
	public $request;


	/**
	 * The current model instance for the form.
	 *
	 * @var mixed
	 */
	protected $model;

	/**
	 * Fields which will not populate
	 *
	 * @var array
	 */
	protected $guarded = [];

	/**
	 * Form attributes
	 *
	 * @var array
	 */
	protected $attributes = [];




	abstract public function prepareForm();

	abstract public function rules(): array;

	public function persist() {
	}


	/**
	 * Set the model instance on the form builder.
	 *
	 * @param  mixed $model
	 * @return void
	 */
	public function setModel($model) {
		$this->model = $model;
	}

	/**
	 * Set the session store for formlets
	 *
	 * @param Session $session
	 */
	public function setSessionStore(Session $session) {
		$this->session = $session;
	}

	/**
	 * Set request on form.
	 *
	 * @param Request $request
	 * @return $this
	 */
	public function setRequest(Request $request) {
		$this->request = $request;
		return $this;
	}

	/**
	 * Add a field to the formlet
	 *
	 * @param AbstractField $field
	 */
	public function add(AbstractField $field) {

		if(is_null($type = $field->getType())){
			$field = $this->setFieldValue($field);
		}else{
			$this->$type($field);
		}

		$this->fields[$field->getName()] = $field;
	}


	/**
	 * Fetch all fields from the form.
	 *
	 * @return array
	 */
	public function fields() {
		return $this->request->all();
	}

	public function render() {

		$errors = $this->getErrors();

		$this->prepareForm();

		$data = [
		  'fields' => $this->getFieldData($this->fields)
		];

		return view($this->view, $data)->withErrors($errors);
	}

	protected function getFieldData(array $fields): array {
		return array_map(function (AbstractField $field) {
			return $field->getData();
		}, $fields);
	}



	/**
	 * Get the value that should be assigned to the field.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @return mixed
	 */
	public function getValueAttribute($name, $value = null, $default = null) {

		// Field should not ne populated from post or from the model
		if (in_array($name, $this->guarded)) {
			return $value;
		}

		if (is_null($name)) {
			return $value;
		}

		if (!is_null($this->old($name)) && $name != '_method') {
			return $this->old($name);
		}

		if (!is_null($value)) {
			return $value;
		}

		if (isset($this->model)) {
			return $this->getModelValueAttribute($name);
		}

		return $default;
	}

	/**
	 * Get a value from the session's old input.
	 *
	 * @param  string $name
	 * @return mixed
	 */
	public function old($name) {
		if (isset($this->session)) {
			return $this->session->getOldInput($name);
		}
	}

	/**
	 * Get the model value that should be assigned to the field.
	 *
	 * @param  string $name
	 * @return mixed
	 */
	protected function getModelValueAttribute($name) {
		//if (method_exists($this->model, 'getFormValue')) {
		//	return $this->model->getFormValue($this->transformKey($name));
		//}
		return data_get($this->model, $name);
	}

	protected function setFieldValue(AbstractField $field): AbstractField {

		$name = $field->getName();
		$value = $field->getValue();
		$default = $field->getDefault();

		$value = $this->getValueAttribute($name, $value, $default);
		$field->setValue($value);

		return $field;
	}


	/**
	 * Returns any errors from the session
	 *
	 * @return array|MessageBag
	 */
	protected function getErrors() {
		$errors = $this->session->get('errors');

		return is_null($errors) ? [] : $errors->getBag('default');
	}

	/**
	 * Get the check state for a checkbox input.
	 *
	 * @param  string $name
	 * @param  mixed  $value
	 * @param  bool   $checked
	 * @return bool
	 */
	protected function getCheckboxCheckedState($name, $value, $checked) {

		if (isset($this->session) && !$this->oldInputIsEmpty() && is_null($this->old($name))) {
			return false;
		}

		if ($this->missingOldAndModel($name)) {
			return $checked;
		}

		$posted = $this->getValueAttribute($name, $checked);

		if (is_array($posted)) {
			return in_array($value, $posted);
		} elseif ($posted instanceof Collection) {
			return $posted->contains('id', $value);
		} else {
			return (bool)$posted;
		}
	}

	/**
	 * Determine if old input or model input exists for a key.
	 *
	 * @param  string $name
	 * @return bool
	 */
	protected function missingOldAndModel($name) {
		return (is_null($this->old($name)) && is_null($this->getModelValueAttribute($name)));
	}

	/**
	 * Determine if the old input is empty.
	 *
	 * @return bool
	 */
	public function oldInputIsEmpty() {
		return (isset($this->session) && count($this->session->getOldInput()) == 0);
	}

	protected function checkable(AbstractField $field){

		$name = $field->getName();
		$value = $field->getValue();
		$default = $field->getDefault();

		$field->setValue($value);

		$checked = $this->getCheckboxCheckedState($name,$value,$default);

		if ($checked) {
			$field->setAttribute('checked');
		}

		return $field;
	}

}
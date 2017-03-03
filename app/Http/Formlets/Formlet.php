<?php

namespace App\Http\Formlets;

use App\Http\Fields\AbstractField;
use App\Http\Fields\Hidden;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

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
	protected $request;

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

	abstract public function prepareForm();

	abstract public function rules(): array;

	abstract public function persist();

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

		$field = $this->setFieldValue($field);

		$this->fields[$field->getName()] = $field;
	}


	public function save() {
		if ($this->isValid()) {
			return $this->persist();
		}
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

		$this->token();

		$this->prepareForm();

		$fields = $this->getFieldsData();

		return view($this->view, compact('fields'))->withErrors($errors);
	}

	protected function getFieldsData(): array {
		return array_map(function (AbstractField $field) {
			return $field->getData();
		}, $this->fields);
	}

	/**
	 * Get the value that should be assigned to the field.
	 *
	 * @param  string $name
	 * @param  string $value
	 * @return mixed
	 */
	public function getValueAttribute($name, $value = null) {

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

		if (isset($this->model)) {
			return $this->getModelValueAttribute($name) ?? $value;
		}

		return $value;
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

		$value = $this->getValueAttribute($name, $field->getValue());
		$field->setValue($value);

		return $field;
	}

	protected function isValid(): bool {
		$this->validate($this->request, $this->rules());

		return true;
	}

	protected function token() {
		$this->add(new Hidden('_token', $this->session->token()));
	}

	/**
	 * Returns any errors from the session
	 *
	 * @return array|MessageBag
	 */
	protected function getErrors(){
		$errors = $this->session->get('errors');

		return is_null($errors) ? [] : $errors->getBag('default');
	}


}
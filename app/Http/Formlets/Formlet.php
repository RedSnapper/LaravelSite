<?php

namespace App\Http\Formlets;

use App\Http\Fields\AbstractField;
use App\Http\Fields\Hidden;
use Illuminate\Contracts\Routing\UrlGenerator;
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
	 * @var UrlGenerator
	 */
	protected $url;

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

	/**
	 * The reserved form open attributes.
	 *
	 * @var array
	 */
	protected $reserved = ['method', 'url', 'route', 'action', 'files'];
	/**
	 * The form methods that should be spoofed, in uppercase.
	 *
	 * @var array
	 */
	protected $spoofedMethods = ['DELETE', 'PATCH', 'PUT'];

	/**
	 * Hidden fields to be rendered by the form
	 *
	 * @var array
	 */

	protected $hidden = [];

	abstract public function prepareForm();

	abstract public function rules(): array;

	public function persist() {
	}

	public function create(array $options = []): Formlet {

		$method = array_get($options, 'method', 'post');

		$attributes['method'] = $this->getMethod($method);

		$attributes['action'] = $this->getAction($options);

		// If the method is PUT, PATCH or DELETE we will need to add a spoofer hidden
		// field that will instruct the Symfony request to pretend the method is a
		// different method than it actually is, for convenience from the forms.
		$this->getAppendage($method);

		$this->attributes = array_merge(
		  $attributes, array_except($options, $this->reserved)
		);

		return $this;
	}

	/**
	 * Parse the form action method.
	 *
	 * @param  string $method
	 * @return string
	 */
	protected function getMethod(string $method): string {
		$method = strtolower($method);
		return $method != 'get' ? 'post' : $method;
	}

	/**
	 * Get the form action from the options.
	 *
	 * @param  array $options
	 * @return string
	 */
	protected function getAction(array $options) {

		// We will also check for a "route" or "action" parameter on the array so that
		// developers can easily specify a route or controller action when creating
		// a form providing a convenient interface for creating the form actions.
		if (isset($options['url'])) {
			return $this->getUrlAction($options['url']);
		}

		if (isset($options['route'])) {
			return $this->getRouteAction($options['route']);
		}

		// If an action is available, we are attempting to open a form to a controller
		// action route. So, we will use the URL generator to get the path to these
		// actions and return them from the method. Otherwise, we'll use current.
		elseif (isset($options['action'])) {
			return $this->getControllerAction($options['action']);
		}
		return $this->url->current();
	}

	/**
	 * Get the action for a "url" option.
	 *
	 * @param  array|string $options
	 * @return string
	 */
	protected function getUrlAction($options) {
		if (is_array($options)) {
			return $this->url->to($options[0], array_slice($options, 1));
		}
		return $this->url->to($options);
	}

	/**
	 * Get the action for a "route" option.
	 *
	 * @param  array|string $options
	 * @return string
	 */
	protected function getRouteAction($options) {
		if (is_array($options)) {
			return $this->url->route($options[0], array_slice($options, 1));
		}
		return $this->url->route($options);
	}

	/**
	 * Get the action for an "action" option.
	 *
	 * @param  array|string $options
	 * @return string
	 */
	protected function getControllerAction($options) {
		if (is_array($options)) {
			return $this->url->action($options[0], array_slice($options, 1));
		}
		return $this->url->action($options);
	}

	/**
	 * Get the form appendage for the given method.
	 *
	 * @param  string $method
	 */
	protected function getAppendage($method) {

		$method = strtoupper($method);
		// If the HTTP method is in this list of spoofed methods, we will attach the
		// method spoofer hidden input to the form. This allows us to use regular
		// form to initiate PUT and DELETE requests in addition to the typical.
		if (in_array($method, $this->spoofedMethods)) {
			$this->hidden [] = new Hidden('_method', $method);
		}

		// If the method is something other than GET we will go ahead and attach the
		// CSRF token to the form, as this can't hurt and is convenient to simply
		// always have available on every form the developers creates for them.
		if ($method != 'GET') {
			$this->token();
		}
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
	 * Set url generator
	 *
	 * @param UrlGenerator $url
	 * @return $this
	 */
	public function setURLGenerator(UrlGenerator $url) {
		$this->url = $url;
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

	public function save() {
		if ($this->isValid()) {
			$this->persist();
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

		$this->prepareForm();

		$data = [
		  'fields'     => $this->getFieldData($this->fields),
		  'attributes' => $this->attributes,
		  'hidden'     => $this->getFieldData($this->hidden)
		];

		return view($this->view, $data)->withErrors($errors);
	}

	protected function getFieldData(array $fields): array {
		return array_map(function (AbstractField $field) {
			return $field->getData();
		}, $fields);
	}

	protected function getHidden(): array {
		return array_map(function (AbstractField $field) {
			return $field->getData();
		}, $this->hidden);
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

	public function isValid(): bool {
		$this->validate($this->request, $this->rules());

		return true;
	}



	protected function token() {
		$this->hidden[] = new Hidden('_token', $this->session->token());
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
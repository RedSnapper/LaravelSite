<?php

namespace App\Http\Formlets;

use App\Http\Fields\AbstractField;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Fields\Hidden;

use Illuminate\Validation\ValidationException;

abstract class Formlet {

	/**
	 * @var UrlGenerator
	 */
	protected $url;

	/**
	 * Session storage.
	 *
	 * @var Session
	 */
	protected $session;

	/**
	 * @var Request
	 */
	public $request;

	protected $view = "forms.auto";

	protected $formView;

	/**
	 * All fields that are added.
	 *
	 * @var AbstractField[]
	 */
	protected $fields = [];

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
	 * Hidden fields to be rendered by the form
	 *
	 * @var array
	 */

	protected $hidden = [];

	/**
	 * Formlets
	 *
	 * @var Formlet[]
	 */

	protected $formlets = [];
	protected $models = [];
	protected $keys = [];

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
	 * The form methods that should be spoofed, in uppercase.
	 *
	 * @var Validator
	 */
	protected $validator;

	/**
	 * Formlet name.
	 *
	 * @var string
	 */
	protected $name = "";

	protected $key;

	public function getKey($name = null) {
		if(is_null($name)) {
			return $this->key;
		} else {
			return @$this->keys[$name];
		}

	}

	abstract public function prepareForm();

	public function rules(): array {
		return [];
	}

	public function addFormlet(string $name,string $class) {
		$formlet = app()->make($class);

		$formlet->setName($name);

		$formlet->prepareForm();

		$formlet->setFieldNames();

		if(isset($this->keys[$name])) {
			$formlet->setKey($this->keys[$name]);
		}

		$this->formlets[$name] = $formlet;
	}

	protected function isValid() {

		$errors = [];

		if (count($this->formlets) == 0) {
			$errors = $this->validate($this->request->all(), $this->rules());
		}

		foreach ($this->formlets as $formlet) {
			$errors = array_merge($errors,$formlet->validate($this->request->get($formlet->getName()), $formlet->rules()));
		}

		return $this->redirectIfErrors($errors);
	}

	protected function redirectIfErrors(array $errors) {

		if(count($errors)){
			throw new ValidationException($this->validator, $this->buildFailedValidationResponse(
			  $errors
			));
		}

		return true;
	}

	/**
	 * Create the response for when a request fails validation.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  array                    $errors
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	protected function buildFailedValidationResponse(array $errors) {
		if ($this->request->expectsJson()) {
			return new JsonResponse($errors, 422);
		}

		return redirect()->to($this->getRedirectUrl())
		  ->withInput($this->request->input())
		  ->withErrors($errors);
	}

	/**
	 * Validate the given request with the given rules.
	 *
	 * @param  array $request
	 * @param  array $rules
	 * @param  array $messages
	 * @param  array $customAttributes
	 * @return array
	 */
	public function validate(array $request, array $rules, array $messages = [], array $customAttributes = []) {
		$this->validator = $this->getValidationFactory()->make($request, $rules, $messages, $customAttributes);

		if ($this->validator->fails()) {
			return $this->formatValidationErrors($this->validator);
		}
		return [];
	}

	public function renderWith($modes) {
		return $this->create($modes)->render();
	}
	public function store() {

		$this->prepareForm();
		$this->assignModels();


		if ($this->isValid()) {
			return $this->persist();
		}
	}

	private function setModels() {
		if (!is_null($this->getKey()) || count($this->keys) > 0) {
			$this->prepareModels();
		}
	}

	protected function prepareModels() {
	}

	public function persist(): Model {
	}

	public function edit(): Model {
	}

	public function update() {
		$this->prepareForm();
		$this->setModels();
		$this->assignModels();

		if ($this->isValid()) {
			return $this->edit();
		}
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	public function setKey($key,$name = null) {
		if(is_null($name)) {
			$this->key = $key;
		} else {
			$this->keys[$name] = $key;
		}
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name) {
		$this->name = $name;
	}

	// Form specific methods

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

	protected function token() {
		$this->hidden[] = new Hidden('_token', $this->session->token());
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

	public function addModel($name,$model) {
		$this->models[$name] = $model;
	}


	/**
	 * Add a field to the formlet
	 *
	 * @param AbstractField $field
	 */
	public function add(AbstractField $field) {

		$this->fields[] = $field;
	}

	/**
	 * Fetch all fields from the form.
	 *
	 * @return array
	 */
	public function fields($name = null) {
		if(is_null($name)) {
			return $this->request->all();
		} else {
			return $this->request->get($name);
		}
	}

	private function assignModels() {
		foreach ($this->formlets as $name => $formlet) {
			if(isset($this->models[$name])) {
				$formlet->setModel([$name => $this->models[$name]]);
			}
			$formlet->assignModels();
		}
	}
	public function render() {
		$this->prepareForm();
		$this->setModels();
		$this->assignModels();

		$this->populate();

		$data = [
		  'form'       => $this->renderFormlets(),
		  'attributes' => $this->attributes,
		  'hidden'     => $this->getFieldData($this->hidden)
		];

		return view($this->formView, $data);
	}

	protected function renderFormlets(): View {

		if (count($this->formlets)) {

			$formlets = [];

			foreach ($this->formlets as $name => $formlet) {
				$formlets[$name] = $formlet->renderFormlets();
			}

			return view($this->view, compact('formlets'));
		} else {
			return $this->renderFormlet();
		}
	}

	public function renderFormlet() {

		$errors = $this->getErrors();

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

	protected function setFieldNames() {
		foreach ($this->fields as $field) {
			$field->setName($this->getFieldPrefix($field->getName()));
		}
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
			return $this->session->getOldInput($this->transformKey($name));
		}
	}

	/**
	 * Transform key from array to dot syntax.
	 *
	 * @param  string $key
	 * @return mixed
	 */
	protected function transformKey($key) {
		return str_replace(['.', '[]', '[', ']'], ['_', '', '.', ''], $key);
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
		return data_get($this->model, $this->transformKey($name));
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

	protected function populate() {

		$this->transformGuardedAttributes();

		foreach ($this->fields as $field) {
			if (is_null($type = $field->getType())) {
				$this->setFieldValue($field);
			} else {
				$this->$type($field);
			}
		}

		foreach($this->formlets as $formlet) {
			$formlet->populate();
		}

	}

	protected function transformGuardedAttributes(){
		$this->guarded = array_map(function($item){
			return $this->getFieldPrefix($item);
		},$this->guarded);

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

	protected function checkable(AbstractField $field) {

		$name = $field->getName();
		$value = $field->getValue();
		$default = $field->getDefault();

		$field->setValue($value);

		$checked = $this->getCheckboxCheckedState($name, $value, $default);

		if ($checked) {
			$field->setAttribute('checked');
		}

		return $field;
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
	 * Set the session store for formlets
	 *
	 * @param Session $session
	 */
	public function setSessionStore(Session $session) {
		$this->session = $session;
	}

	/**
	 * Get a validation factory instance.
	 *
	 * @return \Illuminate\Contracts\Validation\Factory
	 */
	protected function getValidationFactory() {
		return app(Factory::class);
	}

	protected function getModel() : Model {
		if (isset($this->name)) {
			return $this->model[$this->name];
		} else {
			return $this->model;
		}
	}

	/**
	 * Format the validation errors to be returned.
	 *
	 * @param  \Illuminate\Contracts\Validation\Validator $validator
	 * @return array
	 */
	protected function formatValidationErrors(Validator $validator) {

		$errors = collect($validator->errors()->getMessages());

		$errors = $errors->keyBy(function($item,$key){
			return $this->getFieldPrefix($key);
		});

		return $errors->all();
	}

	protected function getFieldPrefix($field){

		$name = $this->getName();

		return $name == "" ? $field : "{$name}[$field]";
	}

	/**
	 * Get the URL we should redirect to.
	 *
	 * @return string
	 */
	protected function getRedirectUrl()
	{
		return app(UrlGenerator::class)->previous();
	}

}
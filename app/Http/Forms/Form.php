<?php

namespace App\Http\Forms;

use App\Http\Fields\AbstractField;
use App\Http\Fields\Hidden;
use App\Http\Formlets\Formlet;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Session\Session;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

abstract class Form {

	use ValidatesRequests;

	protected $view;

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
	 * Form attributes
	 *
	 * @var array
	 */
	protected $attributes = [];

	public function prepare() {
	}

	public function add(string $class, string $name) {
		$this->formlets[$name] = app()->make($class);
	}

	public function create(array $options = []): Form {

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

	public function render() {

		//$errors = $this->getErrors();

		$this->prepare();

		$formlets = [];

		foreach ($this->formlets as $name => $formlet) {
			$formlets[$name] = $formlet->render();
		}

		$data = [
		  'formlets'   => $formlets,
		  'attributes' => $this->attributes,
		  'hidden'     => $this->getFieldData($this->hidden)
		];

		return view($this->view, $data);
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

	protected function token() {
		$this->hidden[] = new Hidden('_token', $this->session->token());
	}

	protected function getFieldData(array $fields): array {
		return array_map(function (AbstractField $field) {
			return $field->getData();
		}, $fields);
	}

	protected function isValid(){

		$rules = [];

		foreach ($this->formlets as $formlet) {
			$rules = array_merge($rules,$formlet->rules());
		}

		$this->validate($this->request, $rules);

		return true;
	}

	public function store(){

		$this->prepare();

		if($this->isValid()){
			dd("Valid");
		}
	}

}
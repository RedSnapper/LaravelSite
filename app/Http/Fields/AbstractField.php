<?php

namespace App\Http\Fields;

use Illuminate\Database\Eloquent\Collection;

abstract class AbstractField {

	/**
	 * Name of the field
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Label value
	 *
	 * @var string|null
	 */
	protected $label;

	/**
	 * View for field
	 *
	 * @var string
	 */
	protected $view;

	/**
	 * Attributes for field
	 *
	 * @var Collection
	 */
	protected $attributes;

	/**
	 * Value of field
	 *
	 * @var mixed
	 */
	protected $value;

	/**
	 * Whether we should populate the field
	 *
	 * @var bool
	 */
	protected $populate = true;

	/**
	 * What type of field this
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Default value for field
	 *
	 * @var string|null
	 */
	protected $default;

	/**
	 * Return the type of field eg. checkable
	 * @return null|string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return null|string
	 */
	public function getDefault() {
		return $this->default;
	}


	/**
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}


	/**
	 * @param mixed $value
	 */
	public function setValue($value) {
		$this->value = $value;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return AbstractField
	 */
	public function setName(string $name): AbstractField {
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @param string $label
	 * @return AbstractField
	 */
	public function setLabel(string $label): AbstractField {
		$this->label = $label;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getView(): string {
		return $this->view;
	}

	/**
	 * @param string $view
	 * @return AbstractField
	 */
	public function setView(string $view): AbstractField {
		$this->view = $view;
		return $this;
	}

	/**
	 * Set the field to be disabled
	 *
	 * @param boolean $disabled
	 * @return AbstractField
	 */
	public function setDisabled($disabled = true): AbstractField {

		$disabled ? $this->setAttribute('disabled')
		  : $this->removeAttribute("disabled");

		return $this;
	}

	/**
	 * Set the field to be disabled
	 *
	 * @param boolean $required
	 * @return AbstractField
	 */
	public function setRequired($required = true): AbstractField {

		$required ? $this->setAttribute('required')
		  : $this->removeAttribute("required");

		return $this;
	}

	/**
	 * Set placeholder
	 *
	 * @param string $string
	 * @return AbstractField
	 */
	public function setPlaceholder(string $string): AbstractField {
		$this->setAttribute('placeholder',$string);
		return $this;
	}


	public function setAttribute(string $attribute, $value=null) {
		$this->attributes->put($attribute, $value ?? $attribute);
		return $this;
	}

	public function setAttributes(array $attributes): AbstractField {
		$this->attributes->merge($attributes);
		return $this;
	}

	public function getData() {
		$attributes = $this->attributes;
		$value = $this->getValue();
		$label = $this->getLabel();
		$name = $this->getName();
		$view = $this->getView();

		return compact('attributes', 'value', 'label', 'name', 'view');
	}

	protected function removeAttribute($key): AbstractField {
		$this->attributes->forget($key);
		return $this;
	}



	function __call($name, $arguments) {
		$value = count($arguments) == 0 ? $name : $arguments[0];
		$this->setAttribute(mb_strtolower($name), $value);
		return $this;
	}

}
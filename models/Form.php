<?php

namespace form;

/**
 * This model contains the outline of forms to be rendered.
 * Forms are built using a form builder admin app for Elefant.
 */
class Form extends \Model {
	/**
	 * Table name.
	 */
	public $table = 'form';

	/**
	 * List of fields that failed validation.
	 */
	public $failed = array ();

	/**
	 * Stores field data.
	 */
	private $_fields = false;

	/**
	 * Stores action data.
	 */
	private $_actions = false;

	/**
	 * A `\Form` object used internally.
	 */
	private $_form;

	/**
	 * Get labels as associative array from fields.
	 */
	public function labels () {
		$labels = array ();
		foreach ($this->field_list as $field) {
			$labels[$field->id] = $field->label;
		}
		return $labels;
	}

	/**
	 * Get a list of validation rules for this form.
	 */
	public function rules () {
		$rules = array ();
		foreach ($this->field_list as $field) {
			$rules[$field->id] = (array) $field->rules;
		}
		return $rules;
	}

	/**
	 * Checks whether the form can be submitted.
	 */
	public function submit () {
		if (! is_object ($this->_form)) {
			$this->_form = new \Form ('POST');
			$this->_form->rules = $this->rules ();
		}
		$res = $this->_form->submit ();
		$this->failed = $this->_form->failed;
		return $res;
	}

	/**
	 * Calls the internal `\Form::merge_values()`.
	 */
	public function merge_values ($obj) {
		return $this->_form->merge_values ($obj);
	}

	/**
	 * Dynamic getter that unserializes fields and actions.
	 */
	public function __get ($key) {
		if ($key == 'field_list') {
			if ($this->_fields === false) {
				$this->_fields = json_decode ($this->data['fields']);
			}
			return $this->_fields;
		} elseif ($key == 'actions') {
			if ($this->_actions === false) {
				$this->_actions = json_decode ($this->data['actions']);
			}
			return $this->_fields;
		}
		return parent::__get ($key);
	}

	/**
	 * Dynamic setter that serializes fields and actions.
	 */
	public function __set ($key, $val) {
		if ($key == 'field_list') {
			$this->_fields = $val;
			$this->data[$key] = json_encode ($val);
			return;
		} elseif ($key == 'actions') {
			$this->_actions = $val;
			$this->data[$key] = json_encode ($val);
			return;
		}
		return parent::__set ($key, $val);
	}
}

?>
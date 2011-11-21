<?php

namespace form;

/**
 * This model contains the outline of forms to be rendered.
 * Forms are built using a form builder admin app for Elefant.
 */
class Form extends \Model {
	/**
	 * Stores field data.
	 */
	private $_fields = false;

	/**
	 * Stores action data.
	 */
	private $_actions = false;

	/**
	 * Get labels as associative array from fields.
	 */
	function labels () {
		$labels = array ();
		foreach ($this->fields as $field) {
			$labels[$field->id] = $field->label;
		}
		return $labels;
	}

	/**
	 * Get a list of validation rules for this form.
	 */
	function rules () {
		$rules = array ();
		foreach ($this->fields as $field) {
			$rules[$field->id] = (array) $field->rules;
		}
		return $rules;
	}

	/**
	 * Dynamic getter that unserializes fields and actions.
	 */
	function __get ($key) {
		if ($key == 'fields') {
			if ($this->_fields = false) {
				$this->_fields = (array) json_decode ($this->data['fields']);
			}
			return $this->_fields;
		} elseif ($key == 'actions') {
			if ($this->_actions = false) {
				$this->_actions = (array) json_decode ($this->data['actions']);
			}
			return $this->_fields;
		}
		return parent::__get ($key);
	}

	/**
	 * Dynamic setter that serializes fields and actions.
	 */
	function __set ($key, $val) {
		if ($key == 'fields') {
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
<?php

namespace form;

/**
 * Provides the JSON API for the form builder.
 */
class API extends \Restful {
	/**
	 * Update the core fields. Usage:
	 *
	 *     /form/api/update/form-id
	 *
	 * Expected POST fields:
	 *
	 *     title
	 *     message
	 *     response_title
	 *     response_body
	 */
	public function post_update ($id) {
		$f = new Form ($id);
		if ($f->error) {
			return $this->error (i18n_get ('Form not found'));
		}
		
		$f->title = $_POST['title'];
		$f->message = $_POST['message'];
		$f->response_title = $_POST['response_title'];
		$f->response_body = $_POST['response_body'];

		$f->put ();
		if ($f->error) {
			return $this->error ('Failed to save changes');
		}
		
		return i18n_get ('Form updated');
	}

	/**
	 * Update the form fields. Usage:
	 *
	 *     /form/api/fields/form-id
	 *
	 * Expects a single POST item named `fields` containing
	 * the data structure of the form fields.
	 */
	public function post_fields ($id) {
		$f = new Form ($id);
		if ($f->error) {
			return $this->error (i18n_get ('Form not found'));
		}

		if (! isset ($_POST['fields'])) {
			return $this->error (i18n_get ('Missing fields parameter'));
		}

		if (! is_array ($_POST['fields'])) {
			return $this->error (i18n_get ('Invalid fields parameter'));
		}

		$ids = array ();
		foreach ($_POST['fields'] as $k => $field) {
			// make sure fields all have unique ids
			if (! isset ($field['id']) || empty ($field['id'])) {
				$field['id'] = preg_replace ('/[^a-z0-9]+/', '_', strtolower ($field['label']));
				while (in_array ($field['id'], $ids)) {
					$field['id'] .= mt_rand (0, 9);
				}
				$ids[] = $field['id'];
				$_POST['fields'][$k]['id'] = $field['id'];
			}

			// split values by newline
			if (isset ($field['values'])) {
				$_POST['fields'][$k]['values'] = explode ("\n", trim ($field['values']));
			}

			// turn rules into list
			if (! isset ($field['rules'])) {
				$_POST['fields'][$k]['rules'] = $this->transform_rules ('');
			} else {
				$_POST['fields'][$k]['rules'] = $this->transform_rules ($field['rules']);
			}
		}

		$f->field_list = $_POST['fields'];

		$f->put ();
		if ($f->error) {
			return $this->error (i18n_get ('Failed to save changes'));
		}

		return i18n_get ('Form updated');
	}

	/**
	 * Translates the rules from the client into the format used
	 * to store rules. See the equivalent function on the client
	 * side that converts the stored rules into the format used
	 * in the UI.
	 */
	function transform_rules ($key) {
		switch ($key) {
			case 'email':
				return (object) array ('email' => 1);
			case 'url':
				return (object) array ('url' => 1);
			case 'numeric':
				return (object) array ('type' => 'numeric');
			case 'alphanumeric':
				return (object) array ('regex' => '/[a-zA-Z0-9]+/');
			case 'alpha':
				return (object) array ('regex' => '/[a-zA-Z]+/');
			case 'yes':
			case 'true':
				return (object) array ('not empty' => 1);
			default:
				return (object) array ();
		}
	}

	/**
	 * Update the form actions. Usage:
	 *
	 *     /form/api/actions/form-id
	 *
	 * Expects a single POST item named `actions` containing
	 * the data structure of the form actions.
	 */
	public function post_actions ($id) {
		$f = new Form ($id);
		if ($f->error) {
			return $this->error (i18n_get ('Form not found'));
		}
	
		if (! isset ($_POST['actions'])) {
			return $this->error (i18n_get ('Missing actions parameter'));
		}
	
		if (! is_array ($_POST['actions'])) {
			return $this->error (i18n_get ('Invalid actions parameter'));
		}
	
		$f->actions = $_POST['actions'];
	
		$f->put ();
		if ($f->error) {
			return $this->error (i18n_get ('Failed to save changes'));
		}

		return i18n_get ('Form updated');
	}
}

?>
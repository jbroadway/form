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

		if (! isset ($_POST['fields']) {
			return $this->error ('Missing fields parameter');
		}

		if (! is_array ($_POST['fields'])) {
			return $this->error ('Invalid fields parameter');
		}

		// make sure fields all have unique ids
		$ids = array ();
		foreach ($_POST['fields'] as $k => $field) {
			if (! isset ($field['id']) || empty ($field['id'])) {
				$field['id'] = preg_replace ('/[^a-z0-9]+/', '_', strtolower ($field['label']));
				while (in_array ($field['id'], $ids)) {
					$field['id'] .= mt_rand (0, 9);
				}
				$ids[] = $field['id'];
				$_POST['fields'][$k]['id'] = $field['id'];
			}
			if (! isset ($field['rules'])) {
				$_POST['fields'][$k]['rules'] = (object) array ();
			}
		}

		$f->field_list = $_POST['fields'];

		$f->put ();
		if ($f->error) {
			return $this->error ('Failed to save changes');
		}

		return i18n_get ('Form updated');
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
			return $this->error ('Missing actions parameter');
		}
	
		if (! is_array ($_POST['actions'])) {
			return $this->error ('Invalid actions parameter');
		}
	
		$f->actions = $_POST['actions'];
	
		$f->put ();
		if ($f->error) {
			return $this->error ('Failed to save changes');
		}

		return i18n_get ('Form updated');
	}
}

?>
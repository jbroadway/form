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

	public function post_fields ($id) {
		$f = new Form ($id);
		if ($f->error) {
			return $this->error (i18n_get ('Form not found'));
		}
	
		if (is_array ($_POST['fields'])) {
			$f->field_list = $_POST['fields'];
		} else {
			$f->field_list = array ();
		}

		$f->put ();
		if ($f->error) {
			error_log ($f->error);
			return $this->error ('Failed to save changes');
		}

		return i18n_get ('Form updated');
	}

	/**
	 * Update the core fields. Usage:
	 *
	 *     /form/api/actions/form-id
	 *
	 * Expected POST fields:
	 *
	 *     actions
	 */
	public function post_actions ($id) {
		$f = new Form ($id);
		if ($f->error) {
			return $this->error (i18n_get ('Form not found'));
		}
	
		if (is_array ($_POST['actions'])) {
			$f->actions = $_POST['actions'];
		} else {
			$f->actions = array ();
		}
	
		$f->put ();
		if ($f->error) {
			return $this->error ('Failed to save changes');
		}

		return i18n_get ('Form updated');
	}
}

?>
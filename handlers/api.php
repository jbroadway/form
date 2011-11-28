<?php

/**
 * Provides the JSON API for the form builder.
 */

$page->layout = false;
header ('Content-Type: application/json');

if (! User::require_admin ()) {
	$this->redirect ('/admin');
}

$error = false;
$msg = '';

// Get and verify parameters
list ($cmd, $id) = $this->params;

if (! $cmd || empty ($cmd)) {
	$error = i18n_get ('No command specified');
} elseif (! $id) {
	$error = i18n_get ('No form specified');
} else {
	switch ($cmd) {
		case 'update';
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
			$f = new form\Form ($id);
			if ($f->error) {
				$error = $f->error;
				break;
			}

			$f->title = $_POST['title'];
			$f->message = $_POST['message'];
			$f->response_title = $_POST['response_title'];
			$f->response_body = $_POST['response_body'];

			$f->put ();
			if ($f->error) {
				$error = $f->error;
				break;
			}
			$msg = i18n_get ('Form updated');
			
			break;
	
		default:
			$error = i18n_get ('Invalid command');
	}
}

$res = new StdClass;
if ($error) {
	$res->success = false;
	$res->error = $error;
} else {
	$res->success = true;
	$res->msg = $msg;
	$res->data = $out;
}

echo json_encode ($res);

?>
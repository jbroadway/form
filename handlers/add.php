<?php

/**
 * Creates a new untitled form and forwards to /form/edit, the form builder.
 */

$page->layout = 'admin';

if (! User::require_admin ()) {
	$this->redirect ('/admin');
}

$f = new form\Form (array (
	'title' => 'Untitled',
	'message' => 'Please fill in the following information.',
	'ts' => gmdate ('Y-m-d H:i:s'),
	'fields' => '[]',
	'actions' => '[]',
	'response_title' => 'Thank you',
	'response_body' => 'Your information has been saved.'
));
$f->put ();
Versions::add ($f);

if (! $f->error) {
	$this->redirect ('/form/edit?id=' . $f->id);
}

$page->title = i18n_get ('An Error Occurred');
echo '<p>' . i18n_get ('Unable to create a new form.') . '</p>';

?>
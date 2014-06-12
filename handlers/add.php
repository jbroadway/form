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

if ($f->error) {
	$page->title = i18n_get ('An Error Occurred');
	echo '<p>' . i18n_get ('Unable to create a new form.') . '</p>';
	echo '<p class="notice">' . $f->error . '</p>';
} else {
	$f = form\Form::query()				// Since the first put for the form doesn't contain the id, it must be fetched from the DB.
		->where('title','Untitled')		// Fetch similar title entries of which one we need.
		->order('id desc')				 // Sort entry id from new to old.
		->single();						// Grab the first entry (the one that was just put in).
	\Versions::add ($f);
	$this->redirect ('/form/edit?id='. $f->id);
}


?>
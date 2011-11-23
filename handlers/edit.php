<?php

/**
 * The form builder.
 */

$page->layout = 'admin';

if (! User::require_admin ()) {
	$this->redirect ('/admin');
}

$f = new form\Form ($_GET['id']);

if ($f->error) {
	$page->title = i18n_get ('An Error Occurred');
	echo '<p>' . i18n_get ('The requested form could not be found.') . '</p>';
	return;
}

$page->add_script ('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js');
$page->add_script ('/apps/form/js/underscore-min.js');
$page->add_script ('/apps/form/js/backbone-min.js');
$page->add_script ('/apps/form/js/formbuilder.js');
$page->add_script ('/apps/form/css/formbuilder.css');

$page->title = i18n_get ('Form Builder');

echo $tpl->render ('form/edit', $f);

?>
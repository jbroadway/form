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

$page->add_script ('/js/jquery-ui/jquery-ui.min.js');
$page->add_script ('/apps/form/js/jquery.tmpl.js');
$page->add_script ('/apps/form/js/knockout-1.2.1.js');
$page->add_script ('/apps/form/js/formbuilder.js');
$page->add_script ('/apps/form/css/formbuilder.css');
$page->add_script ('/apps/form/js/waypoints.min.js');

$page->add_script ('/apps/form/js/jquery.tools.min.js');
$page->add_script ('/apps/form/css/rangeinput.css');
$page->add_script ('/apps/form/css/dateinput.css');

$page->title = i18n_get ('Form Builder');

$o = $f->orig ();
$o->actions = $f->actions;
$o->fields = $f->field_list;

echo $tpl->render ('form/edit', array ('form_data' => $o));

?>
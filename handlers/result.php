<?php

/**
 * Display a single result in full detail.
 */

$page->layout = 'admin';

if (! User::require_admin ()) {
	$this->redirect ('/admin');
}

if (! isset ($_GET['id'])) {
	$this->redirect ('/form/admin');
}

$res = new form\Results ($_GET['id']);
if ($res->error) {
	$this->redirect ('/form/admin');
}

$page->title = i18n_get ('Browsing Result') . ': ' . $res->id;

$labels = $res->form_id ()->labels ();

echo $tpl->render ('form/result', array (
	'data' => $res->results,
	'submitted' => $res->ts,
	'ip' => $res->ip,
	'form' => $res->form_id,
	'labels' => $labels
));

?>
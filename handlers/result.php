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

form\Unread::mark_read ($res->form_id, $res->id, User::val ('id'));

$page->title = i18n_get ('Browsing Result') . ': ' . $res->id;

$labels = $res->form_id ()->labels ();

$fields = (array) $res->results;
foreach ($fields as $k => $v) {
    if (is_array ($v)) {
        $fields[$k] = join (', ', $v);
    } elseif (is_object ($v)) {
    	$fields[$k] = json_encode ($v);
    }
}

echo $tpl->render ('form/result', array (
    'data' => $fields,
    'submitted' => $res->ts,
    'ip' => $res->ip,
    'form' => $res->form_id,
    'labels' => $labels
));

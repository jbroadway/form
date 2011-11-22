<?php

/**
 * Browse a list of results for a specific form.
 */

$page->layout = 'admin';

if (! User::require_admin ()) {
	$this->redirect ('/admin');
}

if (! isset ($_GET['id'])) {
	$this->redirect ('/form/admin');
}

$f = new form\Form ($_GET['id']);
if ($f->error) {
	$this->redirect ('/form/admin');
}

$limit = 20;
$_GET['offset'] = (isset ($_GET['offset'])) ? $_GET['offset'] : 0;

$results = form\Results::query ()
	->order ('ts desc')
	->fetch ($limit, $_GET['offset']);
$count = form\Results::query ()->count ();

// determine which fields to display as columns
// as well as the column names
$labels = $f->labels ();
if ($count > 0) {
	if (count ($labels) === 1) {
		$field_one = array_shift (array_keys ($labels));
		$field_one_name = array_shift (array_values ($labels));
		$field_two_name = '';
		foreach ($results as $k => $v) {
			$res = $v->results;
			$results[$k]->field_one = $res->{$field_one};
			$results[$k]->field_two = '';
		}
	} elseif (count ($data) > 1) {
		$keys = array_keys ($labels);
		$vals = array_values ($labels);
		$field_one = array_shift ($keys);
		$field_one_name = array_shift ($vals);
		$field_two = array_shift ($keys);
		$field_two_name = array_shift ($vals);
		foreach ($results as $k => $v) {
			$res = $v->results;
			$results[$k]->field_one = $res->{$field_one};
			$results[$k]->field_two = $res->{$field_two};
		}
	}
}

$page->title = i18n_get ('Browsing Results') . ': ' . $f->title;
echo $tpl->render ('form/results', array (
	'results' => $results,
	'field_one' => $field_one_name,
	'field_two' => $field_two_name,
	'id' => $_GET['id'],
	'count' => $count,
	'offset' => $_GET['offset'],
	'more' => ($count > $_GET['offset'] + $limit) ? true : false,
	'prev' => $_GET['offset'] - $limit,
	'next' => $_GET['offset'] + $limit
));

?>
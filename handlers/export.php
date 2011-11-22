<?php

/**
 * Export the results of a form as CSV.
 */

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

$page->layout = false;
header ('Cache-control: private');
header ('Content-Type: text/plain');
header ('Content-Disposition: attachment; filename=' . preg_replace ('/[^a-z0-9_-]+/', '-', strtolower ($f->title)) . '-' . gmdate ('Y-m-d') . '.csv');

$results = form\Results::query ()
	->order ('ts desc')
	->fetch_orig ();

$labels = $f->labels ();
echo 'Submitted,IP Address,' . join (',', $labels) . "\n";

foreach ($results as $row) {
	$sep = '';
	echo $row->ts . ',' . $row->ip . ',';
	$res = json_decode ($row->results);
	foreach ($res as $k => $v) {
		$v = str_replace ('"', '""', $v);
		if (strpos ($v, ',') !== false) {
			$v = '"' . $v . '"';
		}
		$v = str_replace (array ("\n", "\r"), array ('\\n', '\\r'), $v);
		echo $sep . $v;
		$sep = ',';
	}
	echo "\n";
}

?>
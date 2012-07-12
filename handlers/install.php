<?php

$page->layout = 'admin';

if (! User::require_admin ()) {
	header ('Location: /admin');
	exit;
}

$cur = $this->installed ('form', $appconf['Admin']['version']);

if ($cur === true) {
	$page->title = 'Already installed';
	echo '<p><a href="/">Home</a></p>';
	return;
} elseif ($cur !== false) {
	header ('Location: /' . $appconf['Admin']['upgrade']);
	exit;
}

$page->title = 'Installing app: form';

if (ELEFANT_VERSION < '1.1.0') {
	$driver = conf ('Database', 'driver');
} else {
	$conn = conf ('Database', 'master');
	$driver = $conn['driver'];
}

$error = false;
$sqldata = sql_split (file_get_contents ('apps/form/conf/install_' . $driver . '.sql'));
foreach ($sqldata as $sql) {
	if (! db_execute ($sql)) {
		$error = db_error ();
		echo '<p class="notice">Error: ' . db_error () . '</p>';
		break;
	}
}

if ($error) {
	echo '<p class="notice">Error: ' . $error . '</p>';
	echo '<p>Install failed.</p>';
	return;
}

echo '<p><a href="/form/admin">Done.</a></p>';

$this->mark_installed ('form', $appconf['Admin']['version']);

?>
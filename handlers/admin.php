<?php

if (! User::require_admin ()) {
	$this->redirect ('/admin');
}

$page->title = 'Forms';
$page->layout = 'admin';

?>
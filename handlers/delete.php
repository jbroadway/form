<?php

/**
 * Delete a form and its associated data.
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

if (! $f->remove ()) {
    $page->title = i18n_get ('An Error Occurred');
    echo '<p>' . i18n_get ('Unable to delete the form.') . '</p>';

    return;
}

// also remove results
DB::execute ('delete from results where form_id = ?', $_GET['id']);

$this->add_notification (i18n_get ('Form deleted.'));
$this->redirect ('/form/admin');

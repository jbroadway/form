<?php

/**
 * Admin page where you can edit forms, view results, and create new forms.
 */

$page->layout = 'admin';

if (! User::require_admin ()) {
    $this->redirect ('/admin');
}

require_once 'apps/form/lib/Functions.php';

$page->title = 'Forms';

$lock = new Lock ();

$forms = form\Form::query ()
    ->order ('title asc')
    ->fetch_orig ();

foreach ($forms as $k => $form) {
    $forms[$k]->locked = $lock->exists ('Form', $form->id);
}

echo $tpl->render ('form/admin', array ('forms' => $forms));

<?php

/**
 * Render a preview of a form based on the form ID. Usage:
 *
 *     /form/preview/form-id
 */

$page->layout = false;

$id = (isset ($this->params[0])) ? $this->params[0] : (isset ($data['id']) ? $data['id'] : false);
if (! $id) {
	// no form specified
	@error_log ('no form specified');
	return;
}

$f = new form\Form ((int) $id);
if ($f->error) {
	// form not found
	@error_log ('form not found');
	return;
}

// render the form
echo '<h1>' . $f->title . '</h1>';

$o = $f->orig ();
$o->failed = $f->failed;
echo $tpl->render ('form/head', $o);

foreach ($f->field_list as $field) {
	if ($field->type == 'date') {
		if ($field->default == 'today') {
			$field->default = gmdate ('Y-m-d');
		}
	}
	echo $tpl->render ('form/field/' . $field->type, $field);
}

echo $tpl->render ('form/previewtail', $o);

echo '<hr />';
echo '<h1>' . $f->response_title . '</h1>';
echo $f->response_body;

?>
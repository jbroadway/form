<?php

/**
 * Returns a list of validation rules as a JSON object so that
 * `js/jquery.verify_values.js` can use the same validation rules
 * client-side that are used by `lib/Form.php` in server-side
 * validation.
 */

if (count ($this->params) != 1) {
    die ('Usage: /form/validator/form_id');
} elseif (! is_numeric ($this->params[0])) {
    die ('Invalid form name');
}

$f = new form\Form ($this->params[0]);
if ($f->error) {
    die ('Form not found');
}

$rules = $f->rules ();
foreach ($rules as $k => $v) {
    if (is_array ($v) && count ($v) === 0) {
        unset ($rules[$k]);
    }
}

$page->layout = false;
header ('Content-Type: application/json');
echo json_encode ($rules);

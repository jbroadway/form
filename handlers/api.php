<?php

/**
 * Calls the JSON API for the form builder.
 */

$this->require_admin ();

$this->restful (new form\API);

?>
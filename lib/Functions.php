<?php

/**
 * List all forms for the dynamic objects dialog.
 */
function form_list_all () {
	return form\Form::query ('id, title')
		->order ('title asc')
		->fetch_assoc ('id', 'title');
}

/**
 * Get a count of the results for a particular form.
 */
function form_results_count ($id) {
	return form\Results::query ()
		->where ('form_id', $id)
		->count ();
}

?>
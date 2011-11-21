<?php

/**
 * List all forms for the dynamic objects dialog.
 */
function form_list_all () {
	return form\Form::query ('id, title')
		->order ('title asc')
		->fetch_assoc ('id', 'title');
}

?>
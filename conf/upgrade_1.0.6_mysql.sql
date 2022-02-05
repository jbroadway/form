create table #prefix#form_read (
	form_id int not null,
	results_id int not null,
	user_id int not null,
	index (form_id, user_id),
	index (results_id, user_id)
);

create table #prefix#form_read (
	form_id integer not null,
	results_id integer not null,
	user_id integer not null
);

create index form_read_form on #prefix#form_read (form_id, user_id);
create index form_read_results on #prefix#form_read (results_id, user_id);

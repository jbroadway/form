create table #prefix#form (
	id integer primary key,
	title char(48) not null,
	message text not null,
	ts datetime not null,
	fields text not null,
	actions text not null,
	response_title char(48) not null,
	response_body text not null
);

create index #prefix#form_title on #prefix#form (title);
create index #prefix#form_ts on #prefix#form (ts);

create table #prefix#form_results (
	id integer primary key,
	form_id int not null,
	ts datetime not null,
	ip char(46) not null,
	results text not null
);

create index #prefix#form_results_form on #prefix#form_results (form_id, ts);

create table #prefix#form_read (
	form_id integer not null,
	results_id integer not null,
	user_id integer not null
);

create index #prefix#form_read_form on #prefix#form_read (form_id, user_id);
create index #prefix#form_read_results on #prefix#form_read (results_id, user_id);

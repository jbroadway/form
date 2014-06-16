create table #prefix#form (
	id serial not null primary key,
	title character varying(48) not null,
	message text not null,
	ts timestamp not null,
	fields text not null,
	actions text not null,
	response_title character varying(48) not null,
	response_body text not null
);
create index title on #prefix#form (title);
create index ts on #prefix#form (ts);

create table #prefix#form_results (
	id serial not null primary key,
	form_id integer not null,
	ts timestamp not null,
	ip character(15) not null,
	results text not null 
);
create index form_id on #prefix#form_results (form_id, ts);
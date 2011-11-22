create table form (
	id integer primary key,
	title char(48) not null,
	message text not null,
	ts datetime not null,
	fields text not null,
	actions text not null,
	response_title char(48) not null,
	response_body text not null
);

create index form_title on form (title);
create index form_ts on form (ts);

create table results (
	id integer primary key,
	form_id int not null,
	ts datetime not null,
	ip char(15) not null,
	results text not null
);

create index results_form on results (form_id, ts);

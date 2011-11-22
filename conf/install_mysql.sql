create table form (
	id int not null auto_increment primary key,
	title char(48) not null,
	message text not null,
	ts datetime not null,
	fields text not null,
	actions text not null,
	response_title char(48) not null,
	response_body text not null,
	index (title),
	index (ts)
);

create table results (
	id int not null auto_increment primary key,
	form_id int not null,
	ts datetime not null,
	ip char(15) not null,
	results text not null,
	index (form_id, ts)
);

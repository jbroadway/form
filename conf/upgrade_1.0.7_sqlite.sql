create table #prefix#form_results_tmp (
	id integer primary key,
	form_id int not null,
	ts datetime not null,
	ip char(46) not null,
	results text not null
);

insert into #prefix#form_results_tmp select * from #prefix#form_results;
drop table #prefix#form_results;
alter table #prefix#form_results_tmp rename to #prefix#form_results;

create index #prefix#form_results_form on #prefix#form_results (form_id, ts);

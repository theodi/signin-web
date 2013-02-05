create table people (
	id varchar(32) not null PRIMARY KEY,
	firstname varchar(255),
	lastname varchar(255),
	email varchar(255),
	company varchar(255),
	photo blob
);
create table in_out (
	id varchar(32) not null,
	checkin varchar(255),
	checkout varchar(255)
);

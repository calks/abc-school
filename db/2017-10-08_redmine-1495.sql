alter table `user` add column is_hidden bool default null;
alter table user_group add column is_hidden bool default null;

update `user` set is_hidden=1 WHERE role='student';
update `user_group` set is_hidden=1;
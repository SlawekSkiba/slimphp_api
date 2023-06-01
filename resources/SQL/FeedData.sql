insert into absence_types (id, name, allowed_days) values (null, 'Vacation', 26);
insert into absence_types (id, name, allowed_days) values (NULL, 'Sick leave', 180);
insert into absence_types (id, name, allowed_days) values (NULL, 'Paternity Leave', 20);
insert into absence_types (id, name, allowed_days) values (NULL, 'Maternity Leave', 20);


insert into positions (id, name, is_admin) values (NULL, 'employee', FALSE);
insert into positions (id, name, is_admin) values (NULL, 'boss', TRUE);
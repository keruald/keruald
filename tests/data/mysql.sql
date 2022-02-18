create database if not exists test_keruald_db;

use test_keruald_db;

drop table if exists numbers;
create table numbers
(
    number_id bigint auto_increment primary key,
    number    int null
);

drop table if exists ships;
create table ships
(
    id       int auto_increment primary key,
    name     varchar(255) null,
    category varchar(3)   null
);

INSERT INTO `ships` VALUES
    (1,'So Much For Subtlety','GSV'),
    (2,'Unfortunate Conflict Of Evidence','GSV'),
    (3,'Just Read The Instructions','GCU'),
    (4,'Just Another Victim Of The Ambient Morality','GCU');

drop view if exists ships_count;
create view ships_count as
select `test_keruald_db`.`ships`.`category`        AS `category`,
       count(`test_keruald_db`.`ships`.`category`) AS `count(category)`
from `test_keruald_db`.`ships`
group by `test_keruald_db`.`ships`.`category`;

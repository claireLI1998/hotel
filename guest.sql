drop table pet_take;
drop table ask_for_ps;
drop table ask_for_mt;
drop table dops;
drop table providemt;
drop table room_maintenance;
drop table reservation;
drop table selectroom;
drop table registerpet;
drop table guest;
drop table pet_free_room;
drop table pet_friendly_room;
drop table hotel_worker;
drop table manager;
drop table pet_service;



create table guest
	(guest_name char(30) not null,
         guest_id number(20) not null,
         email char(30) not null,
	 primary key (guest_id));


create table reservation
	(checkin char(10) not null,
         checkout char(10) not null,
         guest_id number(20) not null,
         rid number(20) not null,
	 primary key (rid),
         foreign key (guest_id) references guest ON DELETE CASCADE); 

grant select on reservation to public;


create table pet_free_room
        (room_number number(30),
         room_price number(30),
         occupy number(1),
         primary key(room_number));


create table pet_friendly_room
        (room_number number(30),
         room_price number(30),
         occupy_pet number(1),
         primary key(room_number));


create table selectroom
	(room_number number(20) not null,
         guest_id number(20) not null,
	primary key (room_number, guest_id));

grant select on selectroom to public;
 

create table registerpet
	(pet_name char(20) not null,
         pet_type char(20) not null,
         guest_id number(20) not null,
	 primary key (pet_name, guest_id)); 


CREATE TABLE pet_service(
          service_type char(30),
          s_id number(5),
          complete number(1),
          primary key(s_id));


CREATE TABLE ask_for_ps(
         s_id number(5),
         guest_id number(20),
         PRIMARY KEY(s_id, guest_id),
         FOREIGN KEY(s_id) REFERENCES pet_service,
         FOREIGN KEY(guest_id) REFERENCES guest);

CREATE TABLE pet_take(
         s_id number(5),
         guest_id number(20),
         p_name char(20),
         PRIMARY KEY(s_id, guest_id, p_name),
         FOREIGN KEY(s_id) REFERENCES pet_service,
         FOREIGN KEY(p_name, guest_id) REFERENCES registerpet);

CREATE TABLE room_maintenance(
          maintanence_type char(20),
          maintenance_id number(10),
          complete number(1),
          PRIMARY KEY(maintenance_id)
);

CREATE TABLE ask_for_mt(
         maintenance_id number(10),
         guest_id number(20),
         PRIMARY KEY(maintenance_id, guest_id),
         FOREIGN KEY(maintenance_id) REFERENCES room_maintenance,
         FOREIGN KEY(guest_id) REFERENCES guest);


create table hotel_worker
	(worker_name char(30) not null,
         worker_id number(20) not null,
         salary_per_hour number(30) not null,
	 primary key (worker_id));

create table manager
	(m_name char(30) not null,
         m_id number(20) not null,
	 primary key (m_id));

create table dops
	(worker_id number(20),
         s_id number(5),
         PRIMARY KEY(worker_id, s_id),
         FOREIGN KEY(worker_id) REFERENCES hotel_worker,
         FOREIGN KEY(s_id) REFERENCES pet_service);

create table providemt
	(worker_id number(20),
         maintenance_id number(10),
         PRIMARY KEY(worker_id, maintenance_id),
         FOREIGN KEY(worker_id) REFERENCES hotel_worker,
         FOREIGN KEY(maintenance_id) REFERENCES room_maintenance);

create table manage_worker


insert into pet_free_room
values(201, 250, 0);

insert into guest
values('a', 2, 'b');

insert into guest
values('claire', 19, 'lin@gmail.com');

grant select on guest to public;

insert into reservation
values ('2020-04-01','2020-04-02', 2, 8800);

insert into pet_free_room
values(202, 250, 0); 

insert into pet_free_room
values(203, 250, 0); 

insert into pet_free_room
values(204, 250, 0); 

insert into pet_free_room
values(205, 250, 0); 

insert into pet_friendly_room
values(301, 300, 0);

insert into pet_friendly_room
values(302, 300, 0);

insert into pet_friendly_room
values(303, 300, 0);

insert into pet_friendly_room
values(304, 300, 0);

insert into pet_friendly_room
values(305, 300, 0);

insert into hotel_worker
values('Jack', 3000, 10);

insert into hotel_worker
values('Peter', 3001, 8);

insert into hotel_worker
values('Mary', 3002, 12);

insert into hotel_worker
values('Rose', 3003, 7);

insert into hotel_worker
values('Pony', 3004, 13);

insert into manager
values('JJ', 999);

insert into room_maintenance
    values('utility repair', 10, 0);
    
insert into pet_service
values('grooming', 100, 0);

insert into dops
values(3004, 100);

insert into providemt
    values(3004, 10);

insert into registerpet
values('Pipi', 'cat', 19);

insert into pet_take
values(100, 19, 'Pipi');

insert into selectroom
values(304, 2);

insert into selectroom
values(303, 19);





grant select on pet_free_room to public;

grant select on pet_friendly_room to public;

grant select on registerpet to public;

grant select on hotel_worker to public;
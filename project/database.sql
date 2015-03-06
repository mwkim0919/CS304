-- CREATE DATABASE practice;
-- use practice;
-- select database();


SET SQL_SAFE_UPDATES=0;



SET foreign_key_checks = 0;
DROP TABLE if exists item;
SET foreign_key_checks = 1;

create table item
	(upc char(12) not null,
	title varchar(30) null,
	itype enum('CD', 'DVD'),
	category enum('rock', 'pop', 'rap','country', 'classical', 'new age', 'instrumental'),
	company varchar(30) null,
	iyear int(4) null,
	price decimal(7,2) null,
	stock char(10) null,
    primary key(upc)
    );
    
SET foreign_key_checks = 0;
DROP TABLE if exists shoppingCart;
SET foreign_key_checks = 1;    

create table shoppingCart
	(
	upc char(12) not null,
	title varchar(30) null,
	itype enum('CD', 'DVD'),
	category char(20) null,
	company varchar(30) null,
	iyear int(4) null,
	price decimal(7,2) null,
	stock char(10) null,
    primary key(upc),
    foreign key(upc) references item(upc)
    on delete cascade
    on update cascade
    );
 
-- grant select on item to public;

SET foreign_key_checks = 0;
drop table if exists leadSinger;
SET foreign_key_checks = 1;

create table leadSinger
	(upc char(12) not null,
	Sname varchar(30) not null,
	primary key(upc, Sname),
    foreign key(upc) references item(upc)
    on delete cascade
    on update cascade
    );
 
-- grant select on leadSinger to public;

SET foreign_key_checks = 0;
drop table if exists hasSong;
SET foreign_key_checks = 1;

create table hasSong
	(upc char(12) not null,
	title varchar(30) not null,
	primary key(upc, title),
    foreign key(upc) references item(upc)
    on delete cascade
    on update cascade
    );
 
-- grant select on hasSong to public;

SET foreign_key_checks = 0;
drop table if exists Corder;
SET foreign_key_checks = 1;

create table Corder
	(receiptId char(30) not null,
	Odate date null,
    cid char(20) null,
    cardNum char(16) null,
    expiryDate date null,
    expectedDate date null,
    deliveredDate date null,
	primary key(receiptId));
 
-- grant select on Corder to public;


SET foreign_key_checks = 0;
drop table if exists purchaseItem;
SET foreign_key_checks = 1;

create table purchaseItem
	(receiptId char(30) not null,
	upc char(12) not null,
    quantity int(10) unsigned not null,
    totalValue decimal(7,2) unsigned null,
	primary key(receiptId, upc),
    foreign key(receiptId) references Corder(receiptId)
    on update cascade -- ,
    -- commented because php file handles foreign key constraint!
    -- foreign key(upc) references item(upc)
    );
  
-- grant select on purchaseItem to public;

SET foreign_key_checks = 0;
drop table if exists customer;
SET foreign_key_checks = 1; 

create table customer
	(cid char(30) not null,
	Cpassword char(30) null,
    Cname char(30) null,
    address char(50) null,
    phone char(20) null,
	primary key(cid));
 
-- grant select on customer to public;

SET foreign_key_checks = 0;
DROP TABLE if exists iReturn;
SET foreign_key_checks = 1;

create table iReturn
	(retid char(30) not null,
	Rdate date null,
    receiptId char(30) not null,
	primary key(retid),
    foreign key(receiptId) references Corder(receiptId)
    on update cascade,
    foreign key(receiptId) references PurchaseItem(receiptId)
    on delete cascade
    on update cascade
    );
  
-- grant select on iReturn to public;

SET foreign_key_checks = 0;
drop table if exists returnItem;
SET foreign_key_checks = 1;

create table returnItem
	(retid char(30) not null,
	upc char(12) not null,
    quantity int(10) null,
	primary key(retid, upc),
	-- commented because php file handles foreign key constraint!
    -- foreign key(upc) references purchaseItem(upc),
	foreign key(retid) references iReturn(retid)
    on update cascade
   
    );
 
-- grant select on returnItem to public;



insert into item 
values ('123456789012', 'Nevermind', 'CD', 'Rock', 'Company 1', 1991, 10.00, 100);

insert into item 
values ('123456789013', 'Thriller', 'DVD', 'Pop', 'Company 2', 1982, 11.00, 50);

insert into item 
values ('123456789014', '8 Mile', 'CD', 'Rap', 'Company 2', 2002, 12.00, 20);

insert into item 
values ('123456789015', 'Based on a True Story', 'DVD', 'Country', 'Company 2', 2013, 8.00, 50);

insert into item 
values ('123456789016', 'Amore', 'CD', 'Classical', 'Company 2', 2006, 9.00, 50);

insert into item 
values ('123456789017', 'Reminiscence', 'CD', 'New Age', 'Company 2', 2010, 13.00, 40);

insert into item 
values ('123456789018', 'Kind of Blue', 'DVD', 'Instrumental', 'Company 2', 2010, 16.00, 70);

insert into shoppingCart 
values ('123456789012', 'Nevermind', 'CD', 'Rock', 'Company 1', 1991, 10.00, 100);

insert into shoppingCart
values ('123456789013', 'Thriller', 'DVD', 'Pop', 'Company 2', 1982, 11.00, 5);

insert into shoppingCart
values ('123456789014', '8 Mile', 'CD', 'Rap', 'Company 2', 2002, 12.00, 20);

insert into leadSinger 
values ('123456789012', 'Nirvana');

insert into leadSinger 
values ('123456789013', 'Michael Jackson');

insert into hasSong 
values ('123456789012', 'Smells Like Teen Spirit');

insert into hasSong 
values ('123456789013', 'Beat It');

insert into Corder 
values ('rec11111', '2014-11-22', 'cid12345', 1234512345013333, '2014-11-22', NULL, NULL);

insert into Corder 
values ('rec22222', '2014-11-22', 'cid54321', 1234567890123456, '2014-11-22', NULL, NULL);

insert into Corder
values ('rec33333', '2014-11-22', 'cid33333', 3333333333333333, '2014-11-22', NULL, NULL);

insert into Corder
values ('rec44444', '2014-11-22', 'cid44444', 4444444444444444, '2014-11-22', NULL, NULL);

insert into Corder
values ('rec55555', '2014-11-15', 'cid55555', 5555555555555555, '2014-11-15', NULL, NULL);

insert into Corder
values ('rec66666', '2014-09-09', 'cid66666', 6666666666666666, '2014-11-15', NULL, NULL);

insert into Corder
values ('rec77777', '2014-11-09', 'cid77777', 7777777777777777, '2014-11-15', NULL, NULL);

insert into purchaseItem
values ('rec11111', '123456789012', 10, NULL);

insert into purchaseItem
values ('rec22222', '123456789013', 30, NULL);

insert into purchaseItem
values ('rec33333', '123456789014', 10, NULL);

insert into purchaseItem
values ('rec44444', '123456789015', 15, NULL);

insert into purchaseItem
values ('rec55555', '123456789016', 20, NULL);

insert into purchaseItem
values ('rec66666', '123456789017', 15, NULL);

insert into purchaseItem
values ('rec77777', '123456789018', 10, NULL);

insert into customer
values ('cid12345', 'pwd12345', 'Cname 1', 'UBC 1', 6040000000);

insert into customer
values ('cid54321', 'pwd54321', 'Cname 2', 'UBC 2', 6041111111);

insert into iReturn
values ('ret11111', '2014-11-11', 'rec11111');

insert into iReturn
values ('ret22222', '2014-10-31', 'rec22222');

insert into returnItem
values ('ret11111', '123456789012', 5);

insert into returnItem
values ('ret22222', '123456789013', 20);

-- should not be inserted



select *
from item

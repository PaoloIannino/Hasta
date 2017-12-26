/*	
	Paolo Iannino s239562

	This script creates the tables needed for the assignment
*/

/*** T A B L E   C R E A T I O N ***/

drop table if exists user;
drop table if exists product;
drop table if exists bids;
drop table if exists winner;

create table s239562.user (
	Email		char(64)	not null, 
	Pwd			char(32)	not null,
	
	primary key (Email)
);

create table s239562.product (
	ProductId	integer		not null,
	Name		char(32),   	
	Brand		char(32), 	
	SerialNo	char(32),   
	State		char(16),   	
	Size		char(16),    
	Weight		char(8),	
	Image		char(255),	
	Description	text,		
	
	primary key (ProductId)
);

create table s239562.bids (
	Email		char(64)	not null,
	ProductId	integer		not null, 
	Bid			float(7, 2) not null,
	
	primary key (Email, ProductId)
);

create table s239562.winner (
	ProductId	integer		not null,
	Email		char(64)	default '',
	Bid 		float(7, 2)	default 1.00,

	primary key(ProductId)
);

insert into s239562.user (Email, Pwd)
	values ('a@p.it', '31f7690f8adde409faa649fba92eba66');

insert into s239562.user (Email, Pwd)
	values ('b@p.it', '886251b342c2cfe1dee77848622bb5d7');

insert into s239562.user (Email, Pwd)
	values ('c@p.it', 'e672fae9e6a3d97892816ce6b4b4736f');

insert into s239562.product (ProductId, Name, Brand, SerialNo, State, Size, Weight, Image, Description)
	values (1, 'Merkava MkIV', 'MANTAK', 'BD0001', 'brand new', '9.04x3.72x2.66m', '65 tons', 'img/MerkavaIV.png', 'The Merkava is an amazing israeli main battle tank successfully used in many recent war theatres. Among its nice features we can enumerate: a 120mm cannon, two 7.62 machine guns, a 60mm mortar and a remote-controlled Browning cal.50 machine gun. It is protected by a composite armor and a Trophy active protection system. Finally, this magnificent is moved by a 1,500 hp turbocharged diesel engine. Unfortunately our offer includes no ammunitions.');

insert into s239562.winner (ProductId)
	values (1);


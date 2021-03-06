drop table  if exists `newrmk`.`users`;
CREATE TABLE `newrmk`.`users` (
  `user_id` INT(11)  AUTO_INCREMENT NOT NULL,
  `user` VARCHAR(80)  NOT NULL,
  `email` VARCHAR(80)  NOT NULL,
  `password` VARCHAR(20)  NOT NULL,
  `active` TINYINT  DEFAULT 1 NOT NULL,
  PRIMARY KEY(`user_id`)
);

insert into `newrmk`.`users` set 
	user="dcarpus", email="csdave2000@yahoo.com", password="";
	

drop table  if exists `newrmk`.`catalogcategories`;
CREATE TABLE `newrmk`.`catalogcategories` (
  `catalogcategories_id` INT(11)  AUTO_INCREMENT NOT NULL,
  `category` VARCHAR(120)  NOT NULL,
  `tag` VARCHAR(20)  NOT NULL,
  `active` TINYINT  DEFAULT 1 NOT NULL,
  PRIMARY KEY(`catalogcategories_id`)
);

drop table  if exists `newrmk`.`knifephotos`;
CREATE TABLE `newrmk`.`knifephotos` (
  `photo_id` INT(11) AUTO_INCREMENT NOT NULL,
  `filelocation` VARCHAR(120)  NOT NULL,
  `photo_labels` VARCHAR(80)  NOT NULL,
  `catalogcategories_id` INT(11)  DEFAULT 0 NOT NULL,
  `active` TINYINT  DEFAULT 1 NOT NULL,
  PRIMARY KEY(`photo_id`)
);

insert into `newrmk`.`catalogcategories` set category="Outdoorsman Knives", tag="outdoorsman";
insert into `newrmk`.`catalogcategories` set category="Skinning and Hunting Knives", tag="hunting";
insert into `newrmk`.`catalogcategories` set category="Saltwater Knives", tag="saltwater";
insert into `newrmk`.`catalogcategories` set category="Survival Knives", tag="survival";
insert into `newrmk`.`catalogcategories` set category="Military Style Knives", tag="military";
insert into `newrmk`.`catalogcategories` set category="Bowie Knives", tag="bowie";
insert into `newrmk`.`catalogcategories` set category="Carving Set & Steak Knife", tag="carving";
insert into `newrmk`.`catalogcategories` set category="Sheaths", tag="sheaths";
insert into `newrmk`.`catalogcategories` set category="Non-Catalog II Knives", tag="noncatalog";
insert into `newrmk`.`catalogcategories` set category="Example Combinations", tag="examples";


drop table  if exists `newrmk`.`knifemodels`;
CREATE TABLE `newrmk`.`knifemodels` (
  `knifemodels_id` INT(11)  AUTO_INCREMENT NOT NULL,
  `catalogcategories_id` INT(11)  DEFAULT 0 NOT NULL,
  `model` VARCHAR(120)  NOT NULL,
  `piclabel` CHAR(2)  NOT NULL,
  `description` TEXT NOT NULL,
  `weight` VARCHAR(60)  NOT NULL,
  `price` VARCHAR(120)  NOT NULL,
  `note` TEXT NOT NULL,
  `active` TINYINT  DEFAULT 1 NOT NULL,
  PRIMARY KEY(`knifemodels_id`)
);


drop table  if exists `newrmk`.`orders`;
CREATE TABLE `newrmk`.`orders` (
  `orders_id` INT(11) AUTO_INCREMENT NOT NULL,
  `processed` TINYINT  DEFAULT 0 NOT NULL,
  `name` VARCHAR(120)  NOT NULL,
  `email` VARCHAR(120)  NOT NULL,
  `address1` VARCHAR(120)  NOT NULL,
  `address2` VARCHAR(120)  NOT NULL,
  `address3` VARCHAR(120)  NOT NULL,
  `city` VARCHAR(120)  NOT NULL,
  `state` VARCHAR(120)  NOT NULL,
  `zip` VARCHAR(20)  NOT NULL,
  `country` VARCHAR(120)  NOT NULL,
  `phone` VARCHAR(20)  NOT NULL,
  `fax` VARCHAR(20)  NOT NULL,
  `shipaddress1` VARCHAR(120)  NOT NULL,
  `shipaddress2` VARCHAR(120)  NOT NULL,
  `shipaddress3` VARCHAR(120)  NOT NULL,
  `ordertype` TINYINT DEFAULT 0  NOT NULL,
  `qty` INT(2)  NOT NULL,
  `model` VARCHAR(120)  NOT NULL,
  `bladelength` VARCHAR(10)  NOT NULL,
  `note` TEXT NOT NULL,
  `cctype` VARCHAR(4)  NOT NULL,
  `ccnumber` VARCHAR(16)  NOT NULL,
  `ccvcode` VARCHAR(6)  NOT NULL,
  `ccexpire` VARCHAR(6)  NOT NULL,
  `ccname` VARCHAR(120)  NOT NULL,
  `datesubmitted` TIMESTAMP  NOT NULL,
  PRIMARY KEY(`orders_id`)
);


drop table  if exists `newrmk`.`faq`;
CREATE TABLE `newrmk`.`faq` (
  `faq_id` INT(11)  AUTO_INCREMENT NOT NULL,
  `question` TEXT NOT NULL,
  `answer` TEXT NOT NULL,
  `active` TINYINT  DEFAULT 1 NOT NULL,
  PRIMARY KEY(`faq_id`)
);


drop table  if exists `newrmk`.`webcopy`;
CREATE TABLE `newrmk`.`webcopy` (
  `webcopy_id` INT(11) AUTO_INCREMENT NOT NULL,
  `page` VARCHAR(60) NOT NULL,
  `prefix` TEXT NOT NULL,
  `postfix` TEXT NOT NULL,
  PRIMARY KEY(`webcopy_id`)
);


drop table  if exists `newrmk`.`emails`;
CREATE TABLE `newrmk`.`emails` (
  `email_id` INT(11)  AUTO_INCREMENT NOT NULL,
  `fromaddress` VARCHAR(60) NOT NULL,
  `toaddress` VARCHAR(60) NOT NULL,
  `messagesubject` TEXT NOT NULL,
  `messagebody` TEXT NOT NULL,
  PRIMARY KEY(`email_id`)
);

/*
--ALTER TABLE `orders` ADD `ccvcode` VARCHAR(6) AFTER `ccnumber`;
--ALTER TABLE `orders` ADD `datesubmitted` TIMESTAMP AFTER `ccname`;
--ALTER TABLE `orders` ADD `address3` VARCHAR(120) AFTER `address2`;
--ALTER TABLE `orders` ADD `shipaddress3` VARCHAR(120) AFTER `shipaddress2`;
--ALTER TABLE `knifephotos` change `photo_labels` `photo_labels` VARCHAR(80)  NOT NULL;
--ALTER TABLE `knifemodels` change `price` `price` VARCHAR(120)  NOT NULL;

--ALTER TABLE `orders` ADD `comment` TEXT AFTER `processed`
*/

insert into `newrmk`.`webcopy` set 
page = "catalog",
prefix="All current catalog and non-catalog information with price lists will be sent via airmail.
There is a charge for catalogs mailed outside the United States.
USA address - no charge
Canada - US$3.00
All Other Countries - US$5.00
Please use this Secure Form, or if you prefer, print this form and fax it to us (407) 855-9054. Minimum age to order is 16 years old", 
postfix="";

insert into `newrmk`.`webcopy` set 
page = "knifecare",
prefix="Fixing/cleaning a knife", 
postfix="";

insert into `newrmk`.`webcopy` set 
page = "faq",
prefix="Frequently Asked Questions", 
postfix="";

insert into `newrmk`.`webcopy` set 
page = "order",
prefix="Current order deliveries are being scheduled in approximately 54 MONTHS. Order limit is two knives every other month per household. We strongly recommend obtaining the catalog to view all features before placing an order. A deposit of US$50.00 per knife is required to place an order. Deposits are not transferable and non-refundable. Credit card user name must match the individual’s name placing the order. Shipping charges are determined by Randall Made Knives based on weight, value and destination in the year of delivery. Mimimum age to order is 16 years old. EFFECTIVE MARCH 1, 2006, RANDALL WILL LIMIT ORDERS TO A SINGLE KNIFE EVERY THREE MONTHS PER HOUSEHOLD.", 
postfix="";




ALTER TABLE `newrmk`.`orders` ADD invoice INT(11);
ALTER TABLE `newrmk`.`orders` ADD amount DECIMAL(7,2);


/*
create index CustID on Customers (CustomerID);
create index KnifeInv on InvoiceEntries (Invoice);

update orders set processed=0 where email='csdave2000@yahoo.com';
select * from orders where email='csdave2000@yahoo.com';

use mysql;
delete from user where user='gtr';
GRANT ALL PRIVILEGES ON *.* to 'gtr'@'carpus'  WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON *.* to 'gtr'  WITH GRANT OPTION;
UPDATE user set password = old_password('xxx') where user = 'gtr';

use mysql;
delete from user where user='rmkweb';
GRANT ALL PRIVILEGES ON *.* to 'rmkweb';
UPDATE user set password = old_password('xxx') where user = 'rmkweb';

use mysql;
delete from user where user='rmkweb';
delete from db where user='rmkweb';
GRANT ALL PRIVILEGES ON newrmk.* to 'rmkweb'@'localhost' IDENTIFIED BY 'xxxx';


SET PASSWORD FOR 'root'@'localhost' = PASSWORD('XXXXX');

FLUSH PRIVILEGES;

http://www.brennan.id.au/17-MySQL_Server.html
*/

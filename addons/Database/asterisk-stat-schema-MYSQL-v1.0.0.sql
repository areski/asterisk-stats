--
-- asterisk-stats database script - Create user & create database for MYSQL
--

-- Usage:
-- mysql -u root -p"root password" < asterisk-stat-mysql-schema-MYSQL-v1.0.0.sql 


--
-- asterisk-stats database - Create database schema
--
 



CREATE TABLE cc_ui_authen (
    userid 									BIGINT NOT NULL AUTO_INCREMENT,
    login 									CHAR(50) NOT NULL,
    password 								CHAR(50) NOT NULL,
    groupid 								INT ,
    perms 									INT ,
    confaddcust 							INT ,
    name 									CHAR(50),
    direction 								CHAR(80),
    zipcode 								CHAR(20),
    state 									CHAR(20),
    phone 									CHAR(30),
    fax 									CHAR(30),
    datecreation 							TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (userid),
    UNIQUE cons_cc_ui_authen_login (login)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_bin;


INSERT INTO cc_ui_authen VALUES (1, 'root', 'myroot', 0, 32767, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2005-02-26 20:33:27.691314-05');


CREATE TABLE cc_system_log (
    id 								INT NOT NULL AUTO_INCREMENT,
    iduser 							INT DEFAULT 0 NOT NULL,
    loglevel	 					INT DEFAULT 0 NOT NULL,
    action			 				TEXT NOT NULL,
    description						MEDIUMTEXT,    
    data			 				BLOB,
	tablename						VARCHAR(255),
	pagename			 			VARCHAR(255),
	ipaddress						VARCHAR(255),	
    creationdate  					TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_bin;





--  type : could be Daily = 0 ; Weekly = 1 ; Monthly = 2
--  hour : selected hour from customer
--  gtm  : selected gtm diference from customer ( i.ex +0200 )
--  time_server : time converted to server gtm


CREATE TABLE `scheduler` (
   `id` bigint(20) NOT NULL auto_increment,
   `type` smallint(5) NOT NULL default '0',
   `days` varchar(14) default '0',
   `hour` smallint(2) NOT NULL default '0',
   `gtm` varchar(5) NOT NULL default '0',
   `time_server` smallint(2) NOT NULL default '0',
   `email` varchar(50) NOT NULL default '',
   `subject` varchar(50) NOT NULL default '',
   `report` TEXT NOT NULL,
   PRIMARY KEY  (`id`),
   KEY `time_server` (`time_server`)
);



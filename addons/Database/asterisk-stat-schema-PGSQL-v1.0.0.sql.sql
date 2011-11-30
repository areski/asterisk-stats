--
-- asterisk-stats database
--

--  Default values - Please change them to whatever you want 
 
-- 	Database name is: asterisk-stats
-- 	Database user is: asuser
-- 	User password is: asuser_pass



-- 1. make sure that the Database user is GRANT to access the database in pg_hba.conf!

--     a line like this will do it
    
--     # TYPE  DATABASE    USER        IP-ADDRESS        IP-MASK           METHOD
--     # Database asterisk/a2billing login with password for a non real user
--     #
--     local   asterisk-stats all						md5
    
--     DON'T FORGET TO RESTART Postgresql SERVER IF YOU MADE ANY MODIFICATION ON THIS FILE
    
-- 2. open a terminal and enter the below commands. We assume our superuser to be postgres.
--    Please adapt to your setup.

--     su - postgres
--     psql -f asterisk-stat-mysql-schema-PGSQL-v1.0.0.sql template1

--     NOTE: the errors you will see about missing tables are OK, it's the default behaviour of pgsql.
    
--     When prompted for the password, please enter the one you choose. In our case, it's 'a2billing'. 




SET default_with_oids = true;


CREATE TABLE cc_ui_authen (
    userid 								BIGSERIAL NOT NULL,
    login 								TEXT NOT NULL,
    "password" 							TEXT NOT NULL,
    groupid 							INTEGER,
    perms 								INTEGER,
    confaddcust 						INTEGER,
    name 								TEXT ,
    direction 							TEXT ,
    zipcode 							TEXT ,
    state 								TEXT ,
    phone	 							TEXT ,
    fax 								TEXT ,
    datecreation 						TIMESTAMP without time zone DEFAULT NOW()
);

ALTER TABLE ONLY cc_ui_authen
    ADD CONSTRAINT cc_ui_authen_pkey PRIMARY KEY (userid);

ALTER TABLE ONLY cc_ui_authen
    ADD CONSTRAINT cons_cc_ui_authen_login_key UNIQUE(login);

	
INSERT INTO cc_ui_authen VALUES (1, 'root', 'myroot', 0, 32767, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2005-02-26 20:33:27.691314-05');


CREATE TABLE cc_system_log (
    id 								BIGSERIAL NOT NULL,
    iduser 							INTEGER NOT NULL DEFAULT 0,
    loglevel	 					INTEGER NOT NULL DEFAULT 0,
    action			 				TEXT NOT NULL,
    description						TEXT,    
    data			 				TEXT,
	tablename						CHARACTER VARYING(255),
	pagename			 			CHARACTER VARYING(255),
	ipaddress						CHARACTER VARYING(255),	
	creationdate  					TIMESTAMP(0) without time zone DEFAULT NOW()   
);
ALTER TABLE ONLY cc_system_log
ADD CONSTRAINT cc_system_log_pkey PRIMARY KEY (id);




/*
  type : could be Daily = 0 ; Weekly = 1 ; Monthly = 2
  hour : selected hour from customer
  gtm  : selected gtm diference from customer ( i.ex +0200 )
  time_server : time converted to server gtm
*/

CREATE TABLE scheduler (
   id serial PRIMARY KEY,
   type integer NOT NULL default '0',
   days text default '0',
   hour integer NOT NULL default '0',
   gtm character varying(5) NOT NULL default '0',
   time_server integer NOT NULL default '0',
   email character varying(50) NOT NULL default '',
   subject character varying(50) NOT NULL default '',
   report text NOT NULL
);
CREATE INDEX time_server_ind ON scheduler USING btree (time_server);






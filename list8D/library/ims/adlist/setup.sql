create database ads;
grant all on ads.* to adsuser@'localhost' identified by 'adspassword';
grant all on ads.* to adsuser@'127.0.0.1' identified by 'adspassword';

use ads;

drop table blti_keys;
create table blti_keys (
     id          MEDIUMINT NOT NULL AUTO_INCREMENT,
     oauth_consumer_key   CHAR(255) NOT NULL,
     secret      CHAR(255) NULL,
     name        CHAR(255) NULL,
     context_id  CHAR(255) NULL,
     created_at  DATETIME NOT NULL,
     updated_at  DATETIME NOT NULL,
     PRIMARY KEY (id)
 );

drop table ads;
create table ads (
     id          MEDIUMINT NOT NULL AUTO_INCREMENT,
     course_key  CHAR(255) NOT NULL,
     user_key    CHAR(255) NULL,
     user_name   CHAR(255) NULL,
     title       CHAR(255) NULL,
     description     TEXT(2048) NULL,
     created_at  DATETIME NOT NULL,
     updated_at  DATETIME NOT NULL,
     PRIMARY KEY (id)
 );

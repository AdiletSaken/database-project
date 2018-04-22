CREATE DATABASE bonus;
CREATE TABLE people (id SERIAL primary key, first_name varchar(50), last_name varchar(50), phone varchar(13), birthday date, email varchar(50) UNIQUE, password char(64));
CREATE TABLE companies (id SERIAL primary key, name varchar(50), email varchar(50) UNIQUE, password char(64));

/*ALTER SEQUENCE people_id_seq RESTART WITH 1;*/
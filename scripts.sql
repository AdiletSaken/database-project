CREATE DATABASE bonus;
CREATE TABLE people (id SERIAL PRIMARY KEY, first_name varchar(50), last_name varchar(50), phone varchar(13), birthday date, code char(8), email varchar(50) UNIQUE, password char(64), company_id int NULL REFERENCES companies (id));
CREATE TABLE companies (id SERIAL PRIMARY KEY, name varchar(50), percentage_full numeric(5, 2), percentage_mixed numeric(5, 2), email varchar(50) UNIQUE, password char(64));
CREATE TABLE company_users(id SERIAL PRIMARY KEY, user_id int REFERENCES people (id), company_id int REFERENCES companies (id), balance numeric(32, 2));
CREATE TABLE transactions(id SERIAL PRIMARY KEY, user_id int REFERENCES people(id), company_id int REFERENCES companies(id), price numeric(32, 2), used numeric(32, 2), added numeric(32, 2), when_happened timestamp(0) DEFAULT now(), type varchar(10));

/*ALTER SEQUENCE people_id_seq RESTART WITH 1;*/
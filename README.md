# phMysql
Object oriented PHP library for building MySQL database schema and quires. 
<p align="center">
  <a href="https://travis-ci.org/usernane/phMysql">
    <img src="https://travis-ci.org/usernane/phMysql.svg?branch=master">
  </a>
  <a href="https://codecov.io/gh/usernane/phMysql">
    <img src="https://codecov.io/gh/usernane/phMysql/branch/master/graph/badge.svg" />
  </a>
  <a href="https://github.com/usernane/phMysql/releases">
      <img src="https://img.shields.io/github/release/usernane/phMySql.svg?label=latest" />
  </a>
  <a href="https://paypal.me/IbrahimBinAlshikh">
    <img src="https://img.shields.io/endpoint.svg?url=https%3A%2F%2Fprogrammingacademia.com%2Fwebfiori%2Fapis%2Fshields-get-dontate-badget">
  </a>
</p>

## API Docs
This library is a part of <a href="https://github.com/usernane/webfiori">WebFiori Framework</a>. To access API docs of the library, you can visid the following link: https://programmingacademia.com/webfiori/docs/phMysql .

## Features
* Ability to create MySQL database table structure using PHP.
* Creating MySQL queries in a simple manner. 
* Connect to MySQL database engine (require mysqli extension).
* Execute MySQL queries and map the result of a query to an object (Like an ORM).
* Validating the data before sending it to the DBMS.

## Supported PHP Versions
The library support all versions starting from version 5.6 up to version 7.4.

## Installation
The easy option is to download the latest release manually from <a href="https://github.com/usernane/phMySql/releases">Release</a>.

## The Idea
The overall idea of the library is as follows, every table in the database is represented as an instance of the class '<a href="https://github.com/usernane/phMysql/blob/master/src/MySQLTable.php">MySQLTable</a>'. The instance is associated with an instance of the class '<a href="https://github.com/usernane/phMysql/blob/master/src/MySQLQuery.php">MySQLQuery</a>'. The main aim of the class 'MySQLQuery' is to construct different types of queries which can be executed and get data from the table. 

The class '<a href="https://github.com/usernane/phMysql/blob/master/src/MySQLLink.php">MySQLink</a>' is used to connect to MySQL database and execute any instance of the class 'MySQLQuery'. In addition to that, it is used to access the data which can be the result of executing a 'select' query.

### Creating Database Tables
The first step in using the library is to create your database tables. As we have said before, every table is represented as an instance of the class <a href="https://programmingacademia.com/webfiori/docs/phMysql/MySQLTable">MySQLTable</a>. Also, we have said that an instance of this class is linked to the class <a href="https://programmingacademia.com/webfiori/docs/phMysql/MySQLQuery">MySQLQuery</a>. 

Let's assume that we want to create a database table with following structure:
* Table name: `users_information`
* Table columns:
 * Column name: `user_id`. Type: `int`. Size: `4`. Primary key.
 * Column name: `username`. Type: `varchar`. Size `25`.
 * Column name: `password`. Type: `varchar`. Size: `64`.
 * Column name: `created_on`. Type: `timestamp` Default to `current_timestamp`.
 * Column name: `last_updated`. Type: `datetime`. Can be `null`.
 
 Such table can be created as follows:
 ``` php
 $query = new MySQLQuery('users_information');
 $query->getTable()->addColumns([
    'user-id'=>[
        'datatype'=>'int',
        'size'=>4,
        'is-primary'=>true
    ],
    'username'=>[
        'datatype'=>'varchar',
        'size'=>25
    ],
    'password'=>[
        'datatype'=>'varchar',
        'size'=>64
    ],
    'created-on'=>[
        'datatype'=>'timestamp',
        'default'=>'current_timestamp'
    ],
    'last-updated'=>[
        'datatype'=>'datetime',
        'is-null'=>true
    ]
 ]);
 ```
### Building Queries
### Connecting to MySQL Database
### Executing MySQL Query
### Fetching Raw Data
### Mapping Query Result to Class Object
### Joining Two Tables
```php


```

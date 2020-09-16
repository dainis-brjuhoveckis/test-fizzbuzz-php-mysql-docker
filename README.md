# FizzBuzz Application — Php, MySQL, Docker, unit/integration tests

## Description

This application is a test assignment implementation, done by [Dainis Brjuhoveckis](mailto:dainis.brjuhoveckis@gmail.com) on September 2020.

It's basically a variation of the famous classic fizzbuzz, this time implemented using PHP 7, MySQL 8, Apache 2, docker-compose.

## Test task specification

### Requirements
- Mysql 8+
- Create database `foo` in your local machine
- Create a table `bar` with three integer columns (a,b,c) in `foo`
- Test task should be completed in PHP 7.4

### Test task
1. Fill the table `bar` with 1 million rows where:
    - column `a` contains the numbers from 1 to 1e6
    - column `b` contains a % 3
    - column `c` contains a % 5
2. Create an endpoint `/databases/foo/tables/bar/csv` which upon a `GET` request responds
with the contents of the corresponding table serialized as CSV and using HTTP chunked
encoding.
3. Create an endpoint `/databases/foo/tables/bar/json` which upon a `GET` request with parameters `page` and `page_size` responds with paginated contents of the corresponding table serialized as JSON. The endpoint must return a JSON array containing objects, which represent the table rows.
4. Write tests.

### Nice to have
- Dockerized environment (docker-compose)

## Installing and running the application

### Installation and running

To build and run the app for the first time, run the following command from the project directory:
```
docker-compose build && docker-compose run composer install && docker-compose up -d
```

Next time app can be started with the usual docker-compose up:
```
docker-compose up -d
```

### Insertion of data

This fills table `foo`.`bar` with data:
```
docker-compose run php -e src/databases/foo/tables/bar/create.php
```
(Could take a minute or two).


### Running tests

```
docker-compose run phpunit --testdox
```

Typical output:
```
PHPUnit 9.3.10 by Sebastian Bergmann and contributors.

Fizz Buzz Lib
 ✔ Fizz buzz abc 1
 ✔ Fizz buzz abc 15
 ✔ Fizz buzz abc 1 to 7 with data set #0
 ✔ Fizz buzz abc 1 to 7 with data set #1
 ✔ Fizz buzz abc 1 to 7 with data set #2
 ✔ Fizz buzz abc 1 to 7 with data set #3
 ✔ Fizz buzz abc 1 to 7 with data set #4
 ✔ Fizz buzz abc 1 to 7 with data set #5
 ✔ Fizz buzz abc 1 to 7 with data set #6

Fizz Buzz Integration Db
 ✔ Table row count 1000000
 ✔ Table row a 1
 ✔ Table row a 1000000
 ✔ Table row a from 1 to 7
 ✔ Table rows a from 123456 to 123458
 ✔ Table rows a from 999998 to 1000000

Fizz Buzz Integration Web Api
 ✔ Json
 ✔ Json page 2 page size 3
 ✔ Csv headers
 ✔ Csv file contents

Time: 00:03.461, Memory: 86.39 MB

OK (19 tests, 34 assertions)
```

### Uninstallation

```
docker-compose down --rmi all && sudo rm -rf var vendor .phpunit.result.cache
```

## Design considerations and implementation notes

### PHP code

The design motto for this application: “as simple as possible, but not simpler”, KISS and YAGNI.

No attempt was made to create another framework with configuration, routing, logging, dependency injection, etc. For this there are really awesome frameworks: Laravel, Symfony, Yii, Slim just to name a few. PHP language features like namespaces, classes, ... were not used this time, except in the unit tests.

App web service endpoint URL such as `http://localhost:20080/databases/foo/tables/bar/json` suggests that `foo` and `bar` could actually be variable parameters (`/databases/:database/tables/:table/json`), so the app could be used to view contents of any table in any database (schema) as long as MySQL user has the _select_ permission. This parametrization could be implemented in numerous ways, for example by using `mod_rewrite` (Apache web server module), but usually when there's request for such functionality, it's time to use a framework like Laravel or Symfony, where such URL params functionality is provided out of the box and easily implemented by app's code.

### Database code

As of now, filling table `foo.bar` with 1 000 000 records takes about 1 minute 20 seconds on author's machine.

There are several “tricks” to (maybe) make this run faster, these come to author's mind:
- Insert several rows at once  `insert into bar (a, b, c) values (...)(...)(...)(...);`.
- Insert just about 15 rows into table and then do `insert into bar (a, b, c) select (a+15, b, c) from bar where ...` until the necessary count is reached.
- Create a file and then use MySQL's `LOAD DATA LOCAL INFILE ...`.

But that would make the application's logic more complex so the author avoided that.

## Web service API functions

### CSV

GET http://localhost:20080/databases/foo/tables/bar/csv

Returns file that contains all the records from database table `foo.bar`.

Example: 
```
a,b,c
1,1,1
2,2,2
3,0,3
4,1,4
<skipped>
999999,0,4
1000000,1,0
```

### JSON

GET http://localhost:20080/databases/foo/tables/bar/json
or
GET http://localhost:20080/databases/foo/tables/bar/json?page=:page&page_size=:page_size (page and page_size can be any positive integer).

Examples:
http://localhost:20080/databases/foo/tables/bar/json
```
[{"a":"3","b":"0","c":"3"},{"a":"4","b":"1","c":"4"}]
```

http://localhost:20080/databases/foo/tables/bar/json?page=3&page_size=5
```
[{"a":"11","b":"2","c":"1"},{"a":"12","b":"0","c":"2"},{"a":"13","b":"1","c":"3"},{"a":"14","b":"2","c":"4"},{"a":"15","b":"0","c":"0"}]
```

### Destroy

GET http://localhost:20080/databases/foo/tables/bar/destroy

Drops the table `foo.bar`.

To re-create the data deleted by _destroy_, the _create_ endpoint can be used (see next section).

### Create

GET http://localhost:20080/databases/foo/tables/bar/create

Creates table `foo.bar` and fills it with data.

Logic:
- If there is table `foo.bar`, then does nothing.
- If there is no table `foo.bar`, then creates the table `foo.bar` and fills it with one million records.

Note that it runs for about 1 minute 20 seconds.

## Additional tools 

### Adminer

This app contains also image of Adminer, a tool for working with databases, such as selecting data from tables, creating tables, making exports and imports. It is available at http://localhost:28080/?server=mysql&username=foo&db=foo (Password is "foo").

When the application is running, clicking on [select bar](http://localhost:28080/?server=mysql&username=foo&db=foo&select=bar) in Adminer would show the rows in table `foo.bar`.

### XDebug

The php-apache image has XDebug configured, so it connects to port 9000 whenever PHP script is executed. That allows for debugging in Visual Studio Code, PHPStorm and so on.

#### Linux

On Linux Docker does not have special hostname entry host.docker.internal, so this is emulated by env variable:
```
export DOCKER_XDEBUG_REMOTE_HOST=$(ip addr show | grep "\binet\b.*\bdocker0\b" | awk '{print $2}' | cut -d '/' -f 1)
```

The above command could be added to ~/.bashrc to spare the need running this every time.

As far as the author of this document knows, this is not required on Windows and Mac hosts, but that is not 100% certain as it is not tested by the author.

#### Visual Studio Code — Example launch configuration

Example XDebug configuration for [Visual Studio Code editor](https://code.visualstudio.com):
```
{    
    "version": "0.2.0",
    "configurations": [
        {            
            "name": "Listen for XDebug",
            "type": "php",
            "request": "launch",
            "stopOnEntry": false,
            "port": 9000,
            "pathMappings": {
                "/var/www/html": "${workspaceFolder}/src"
            },
            "xdebugSettings": {
                "max_data": 65535,
                "show_hidden": 1,
                "max_children": 100,
                "max_depth": 5
            }
        }
    ]
}
```
or
```
...
            "pathMappings": {
                "/var/www/html": "${cwd}/src"
            }
...
```

# OTP Registration Form API

An API for user registration and phone verification via one time SMS password. Requires **PHP 8**

## Getting Started
First, clone the repo
```bash
$ git clone https://github.com/lgadzhev/otp-api.git
```

#### Install dependencies
```bash
$ cd otp-api
$ composer install
```

#### Configure the Environment
Create `.env` file:
```bash
$ cat .env.example > .env
``` 

Edit database name, database username and database password.

#### Database import

First, we need to create the database. Replace `root` with your mysql username and `smsapi` with your database name 
from .env and hit Enter. Fill your password when asked. 
```bash
$ mysql -u root -p -e "CREATE DATABASE smsapi;"
```
Then import the database:
```bash
$ mysql -u root -p smsapi < database.sql
```

#### Start the API

Execute the PHP build in server to start listening for requests

```bash
$ php -S 127.0.0.1:8000 -t public/   
```

#### PHPUnit testing

Execute to run the PHPUnit tests

```bash
$ composer test
```

### API Endpoints
| HTTP Verbs | Endpoints        | Action                         | Fields                                                                 |
|------------|------------------|--------------------------------|------------------------------------------------------------------------|
| POST       | /v1/register     | To register a new user         | _string_: **email**<br/>_string_: **password**<br/>_string:_ **phone** |
| POST       | /v1/verify-phone | To to verify the user's phone  | _string_: **code**<br/>_int_: **user_id**                              |
| POST       | /v1/new-code     | To create a new code           | _int_: **user_id**                                                     |


### Database diagram

![Database diagram](https://github.com/lgadzhev/otp-api/blob/main/database-diagram.png?raw=true)

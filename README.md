# Laravel dynamic user connection

If you need to have separate databases for each user, then you are on the right place. This is a laravel package which helps you setup the dynamic user database connections in just few seconds.


## Installation : 

~~~bash
composer require techsemicolon/laravel-dynamic-user-connection
~~~

Then add `DynamicConnectionServiceProvider` entry in `config/app.php`'s `providers` array : 

~~~php
Techsemicolon\DynamicConnection\DynamicConnectionServiceProvider::class,
~~~


## How it works : 

You should have basic mysql database collection which has `users` table. Basic login authentication will work from there as usual. 

The package comes into picture once user is logged in, and switches the database as per applicable settings in `App\User.php` model.


## Usage :

The package expects you to save these details in `users` table. What package needs is the column name where the information for dynamic user connection is stored.

You can set database by adding following public property in `App\User.php` model : 

~~~php
<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    public $dynamic_connection_database = 'database_name';
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

        'password', 'remember_token',
    ];

}

~~~

Above settings will take the string value stored in `database_name` column of `users` table as the database name for that user's connection.

You can optionally specify username and password columns as well : 

~~~php
<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    public $dynamic_connection_database = 'database_name';

    // Optional
    public $dynamic_connection_username = 'database_username';
    public $dynamic_connection_password = 'database_password';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

        'password', 'remember_token',
    ];

}

~~~

Note : you need to decrypt() the database_password column value while storing into `users` table using laravel's `decrypt()` helper. This has been done as it is not safe to store passwords directly as a string in the database.

If password is not encryptable then package throws `DynamicConnectionInvalidPasswordException`.

## License : 

This package is open-sourced software licensed under the MIT license

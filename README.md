# Migrator
A simple php database migrations library and management tool. This is standalone
database migration library that is usable in any platform with any database.

Its in very early development but usable. You need to know what your doing to
use this.

## Install
This should be installed from packagist with composer:
``` shell
$ composer require lightscale/migrator
```

## Configuration
You need to do two things to use this. Copy the command line tool that manages
migrations to the root of your project. It needs to be the same directory as
`/vendor`.

``` shell
$ cp vendor/lightscale/migrator/bin/migrator.php ./migrator
```

You then need a config file. This is where things get tricky as in here you
to give migrator callback functions to get the current migration version and
set it. You also need to create a function that provides what over database
object you need to be able to modify your database.

Here is a very basic template:

``` php
<?php

function init() {
    /*
     * This runs in the migrator constructor to allow you to setup
     * any more general things.
     *
     * An example would be load any components of your application that
     * are required to make this work. For example in wordpress you need
     * to require "wp-load.php" to initialize wordpress and be able to use
     * its database and functions.
     */
}

function version_get($db) {
    /*
     * This needs to access where you have stored the current migrations verison
     * and return it.
     *
     * Read version_update for more info
     */
}

function version_update($db, $version) {
    /*
     * This needs to create some form of storage for the version and update it
     * when ever the version changes. This could be done with a file or a
     * database table. Anything you like.
     *
     * For example in wordpress you can use wp_options and the options API.
     *
     * For a standalone project you can create a database table with a single
     * row and column to hold the version.
     */
}

function get_db() {
    /*
     * This needs to return an object that gives access in some form to the
     * database or databases that you need to migrate.
     *
     * This will be the only thing that is passed to the up and down functions
     * within the migrations.
     *
     * This is really what makes this completely independent of what you are
     * using this library on.
     */
}


/*
 * This file needs to return the configuration in an associative array.
 */
return [
    'init' => 'init',
    'migrations_dir' => 'database_migrations', // Required
    'version_update_fn' => 'version_update',   // Required
    'version_get_fn' => 'version_get',         // Required
    'get_db_fn' => 'get_db'                    // Required
];

```

Of course you might want to stick it in a class or namespace to avoid name
collisions. Just make sure that the config file returns an associative array
that contains the required properties.

Check out
[lightscale/migrator-config-wordpress](https://github.com/lightscaletech/migrator-config-wordpress)
for a working example.

## Usage

Run the `./migrator` script. This will output the help.

Here is the available commands:

```
  create    Create a new migration
  help      Displays help for a command
  list      Lists commands
  migrate   Run all remaining migrations
  reset     Rollback all migrations.
  rollback  Undo one migration
```

## Migrations

Here is the file structure of the migrations. You can specify the directory in
the config and then its just flat:

```
/
└── dbmigrations
    ├── 191026_144428_test_mig_1.php
    ├── 191026_144432_test_mig_2.php
    └── 191026_144435_test_mig_3.php
```

This is the structure of a migration:

``` php
<?php

use Lightscale\Migrator\Migration;

class example implements Migration {

    public function up($db) {
        /*
         * Called when migrate is run.
         *
         * The DB parameter is the value
         * returned by "get_db_fn" from the config.
         */
    }

    public function down($db) {
        /*
         * Called when rollback or reset.
         *
         * The DB parameter is the value
         * returned by "get_db_fn" from the config.
         */
    }

}

```

## Release history
- 0.0.2
  - Feature - ability to set a class to extend migrations with.
  - Bug - update version to last successful when a migration fails.
  - Bug - fixes to migrator_dir handling so it can find migrations.
- 0.0.1 All core functionality working. Still in on-going development.

## Requirements
- PHP 7.1.3
- Composer

## Contributors
Sam Light

## Licence
This project is licensed under the GPLv3 License - see the LICENSE file for
details.

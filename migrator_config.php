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

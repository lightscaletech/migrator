<?php

function version_get($db) {

}

function version_update($db, $version) {

}

function get_db() {

}

return [
    'migrations_dir' => 'database_migrations',
    'version_update_fn' => 'version_update',
    'version_get_fn' => 'version_get',
    'get_db_fn' => 'get_db'
];

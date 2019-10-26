<?php

function version_get() {
    //return '191025_152617';
    return '191026_090245';
}

function version_update($version) {
    var_dump($version);
}

function get_db() {
    return 'database';
}

return [
    'migrations_dir' => 'database_migrations',
    'version_update_fn' => 'version_update',
    'version_get_fn' => 'version_get',
    'get_db_fn' => 'get_db'
];

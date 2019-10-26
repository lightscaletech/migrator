<?php

namespace Lightscale\Migrator;

use Symfony\Component\Console\Application;

class Console {
    public static function run($config = './migrator_config.php') {
        $app = new Application('Migrator', '0.0.1-dev');

        $migrator = new Migrator($config);

        $app->add(new Commands\NewMigration($migrator));
        $app->add(new Commands\Migrate($migrator));
        $app->add(new Commands\Rollback($migrator));
        $app->add(new Commands\Reset($migrator));

        $app->run();
    }
}

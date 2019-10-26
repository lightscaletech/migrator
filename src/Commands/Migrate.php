<?php

namespace Lightscale\Migrator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Migrate extends Command {
    protected static $defaultName = 'migrate';

    private $migrator = NULL;

    public function __construct($migrator, $base = '') {
        self::$defaultName = $base . self::$defaultName;
        parent::__construct();

        $this->migrator = $migrator;
    }


    protected function configure() {
        $this->setDescription('Run all remaining migrations')
             ->setHelp('This command allows you to run all migrations ' .
                       'from the current state to the lastest migration.');
    }

    protected function execute(InputInterface $in, OutputInterface $out) {
        $logfn = function($msg, $error = false) use($out) {
            if($error) $msg = '<error>' . $msg . '</error>';

            $out->writeln($msg);
        };

        if($name = $this->migrator->migrate($logfn)) {
            $out->writeln([
                '<info>',
                'Successfully run migrations',
                '</info>'
            ]);
        }
        else {
            $out->writeln([
                '', '<error>Failed run all migrations</error>', ''
            ]);
        }
    }

}

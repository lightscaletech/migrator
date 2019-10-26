<?php

namespace Lightscale\Migrator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Reset extends Command {
    protected static $defaultName = 'reset';

    private $migrator = NULL;

    public function __construct($migrator, $base = '') {
        self::$defaultName = $base . self::$defaultName;
        parent::__construct();

        $this->migrator = $migrator;
    }

    protected function configure() {
        $this->setDescription('Rollback all migrations.')
             ->setHelp('This command allows you to rollback all migrations ' .
                       'from the current version.');
    }

    protected function execute(InputInterface $in, OutputInterface $out) {
        $logfn = function($msg, $error = false) use($out) {
            if($error) $msg = '<error>' . $msg . '</error>';

            $out->writeln($msg);
        };

        if($name = $this->migrator->reset($logfn)) {
            $out->writeln([
                '<info>',
                'Successfully reset the database',
                '</info>'
            ]);
        }
        else {
            $out->writeln([
                '', '<error>Failed to reset the database </error>', ''
            ]);
        }
    }
}

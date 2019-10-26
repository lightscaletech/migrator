<?php

namespace Lightscale\Migrator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Rollback extends Command {
    protected static $defaultName = 'rollback';

    private $migrator = NULL;

    public function __construct($migrator, $base = '') {
        self::$defaultName = $base . self::$defaultName;
        parent::__construct();

        $this->migrator = $migrator;
    }

    protected function configure() {
        $this->setDescription('Undo one migration')
             ->setHelp('This command allows you to run the down method on ' .
                       'last migration that was run');
    }

    protected function execute(InputInterface $in, OutputInterface $out) {
        $logfn = function($msg, $error = false) use($out) {
            if($error) $msg = '<error>' . $msg . '</error>';

            $out->writeln($msg);
        };

        if($name = $this->migrator->rollback($logfn)) {
            $out->writeln([
                '<info>',
                'Successfully rolledback migration',
                '</info>'
            ]);
        }
        else {
            $out->writeln([
                '', '<error>Failed rollback migration</error>', ''
            ]);
        }
    }
}

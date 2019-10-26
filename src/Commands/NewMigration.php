<?php

namespace Lightscale\Migrator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class NewMigration extends Command {
    protected static $defaultName = 'create';

    private $migrator = NULL;

    public function __construct($migrator, $base = '') {
        self::$defaultName = $base . self::$defaultName;
        parent::__construct();

        $this->migrator = $migrator;
    }

    protected function configure() {
        $this->setDescription('Create a new migration')
             ->setHelp('This command allows you to create a new migration. ' .
                       'It requires that you set the name of the migration ' .
                       'and thats it')
             ->addArgument('name', InputArgument::REQUIRED,
                           'The name of the migration');
    }

    protected function execute(InputInterface $in, OutputInterface $out) {
        if($name = $this->migrator->createMigration($in->getArgument('name'))) {
            $out->writeln([
                '<info>',
                'Successfully created new migration named:',
                '', $name, '</info>'
            ]);
        }
        else {
            $out->writeln([
                '', '<error>Failed to create new migration</error>', ''
            ]);
        }
    }

}

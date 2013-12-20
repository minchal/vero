<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Tools\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console;

class DatabaseExport extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            -> setName('dbal:export')
            -> setDescription('Export database to SQL file.')
            -> addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'File, where to put SQL dump.', 'dump.sql')
            -> setHelp(
                <<<EOT
Dump database structure and data to file.

Currently supported drivers:
 - mysql
EOT
            );
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, OutputInterface $output)
    {
        $config = $this -> getHelper('di') -> getContainer() -> get('config');
        $file = $input -> getOption('file');
        
        switch ($driver = $config -> get('database.driver')) {
            case 'pdo_mysql':
                $this -> dumpMySQL($config, $file, $output);
                break;
            default:
                throw new \RuntimeException("Driver '$driver' not supported in export command.");
        }
        
        $output -> writeln('<info>Export completed.</info>');
    }
    
    private function dumpMySQL($config, $file, OutputInterface $output)
    {
        $command = sprintf(
            'mysqldump --no-create-db --user=%s --password=%s %s > %s',
            $config->get('database.user'),
            $config->get('database.password'),
            $config->get('database.dbname'),
            $file
        );
        
        exec($command, $result, $return);
        
        if ($return) {
            $output -> writeln('<error>Command:</error> '.$command);
            throw new \RuntimeException('Mysqldump exited with errors!');
        }
    }
}

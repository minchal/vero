<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Tools\Console\Command;

use Vero\Filesystem\Directory;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console;

/**
 * Console vero:compact-libs command.
 */
class CompactLibs extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            -> setName('vero:compact-libs')
            -> setDescription('Compact libraries downloaded by Composer to single namespace.')
            -> addOption('source', 's', InputOption::VALUE_OPTIONAL, 'The path with Composer libraries.', './vendor')
            -> addOption('dest', 'd', InputOption::VALUE_OPTIONAL, 'The path to save compacted files.', './lib')
            -> setHelp(
<<<EOT
Copy libraries downloaded by Composer to common directory.

Libraries namespaces are from Composer-created file <comment><source>/composer/autoload_namespaces.php</comment>.
EOT
            );
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $source = $input -> getOption('source');
        $dest   = $input -> getOption('dest');
        
        $libs = require $source.'/composer/autoload_namespaces.php';
        $count = 0;
        
        foreach ($libs as $ns => $dirs) {
            list($ns) = explode('\\', $ns);
            list($ns) = explode('_', $ns);
            
            foreach ($dirs as $dir) {
                $from = new Directory($dir .'/'. $ns);
                $from -> copy($dest .'/'. $ns);
                $count++;
            }
        }
        
        $output -> writeln('<info>Successfully copied '.$count.' libraries.</info>');
    }
}

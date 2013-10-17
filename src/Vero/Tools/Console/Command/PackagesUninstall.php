<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Tools\Console\Command;

use Vero\Tools\PackagesManager\Manager;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console;

/**
 * Console vero:packages:uninstall command.
 */
class PackagesUninstall extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            -> setName('vero:packages:uninstall')
            -> setDescription('Uninstall Vero packages.')
            -> addArgument('package', InputArgument::IS_ARRAY, 'Packages names to uninstall')
            -> addOption('dest', 'd', InputOption::VALUE_OPTIONAL, 'Change destination directory')
            -> setHelp(
<<<EOT
Uninstall Vero CMS packages.

You can configure repository path in <comment>repository</comment> variable in main config file.
EOT
            );
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $container = $this -> getHelper('di') -> getContainer();
        
        $mngr = new Manager(
            $container->get('config')->get('repository'),
            $input->getOption('dest') ? $input->getOption('dest') : $container->get('app')->path('base')
        );
        
        $listener = function ($node, $status) use ($output) {
            $output -> write('   ');
            
            if ($status) {
                $output -> write('<info>deleted</info>    ');
            } else {
                $output -> write('<error>not found</error>  ');
            }
            
            $output -> writeln(' '.$node);
        };
        
        foreach ($input->getArgument('package') as $name) {
            $package = $mngr -> getPackage($name);
            
            $output -> writeln('Package <comment>'.$package->getname().'</comment>:');
            
            $mngr -> uninstall($package, $listener);
        }
    }
}

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
 * Console vero:packages:status command.
 */
class PackagesStatus extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            -> setName('vero:packages:status')
            -> setDescription('Check, if package is installed.')
            -> addArgument('package', InputArgument::IS_ARRAY, 'Packages names to check')
            -> addOption('deps', null, InputOption::VALUE_NONE, 'With dependencies')
            -> addOption('dest', 'd', InputOption::VALUE_OPTIONAL, 'Change destination directory')
            -> setHelp(
<<<EOT
Check installation of Vero CMS packages.

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
            $output -> write('      ');
            
            if ($status) {
                $output -> write('<info>installed</info>     ');
            } else {
                $output -> write('<error>not found</error>     ');
            }
            
            $output -> writeln(' '.$node);
        };
        
        $printStatus = function ($status) use ($output) {
            switch($status) {
                case 2:
                    $output -> writeln('<comment>partial</comment>');
                    break;
                case 1:
                    $output -> writeln('<info>installed</info>');
                    break;
                case 0:
                    $output -> writeln('<error>not found</error>');
                    break;
            }
        };
        
        $packages = $mngr -> resolvePackages($input->getArgument('package'), (boolean) $input->getOption('deps'));
        
        foreach ($packages as $package) {
            $output -> writeln('Package <comment>'.$package->getname().'</comment>:');
            $output -> writeln('   dependencies:');
            
            foreach ($package->getDeps() as $dep) {
                $output -> write('      '.str_pad($dep, 15));
                $printStatus($mngr -> checkStatus($mngr -> getPackage($dep)));
            }
            
            $output -> writeln('   files:');
            
            $totalStatus = $mngr -> checkStatus($package, $listener);
            
            $output -> writeln('   package status:');
            $output -> write('      ');
            $printStatus($totalStatus);
        }
    }
}

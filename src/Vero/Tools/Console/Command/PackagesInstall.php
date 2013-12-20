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
 * Console vero:packages:install command.
 */
class PackagesInstall extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            -> setName('vero:packages:install')
            -> setDescription('Install Vero packages from common repository.')
            -> addArgument('package', InputArgument::IS_ARRAY, 'Packages names to uninstall')
            -> addOption('force', null, InputOption::VALUE_NONE, 'Owerwrite existing files')
            -> addOption('deps', null, InputOption::VALUE_NONE, 'With dependencies')
            -> addOption('dest', 'd', InputOption::VALUE_OPTIONAL, 'Change destination directory')
            -> setHelp(
<<<EOT
Install Vero CMS packages.

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
            
            switch ($status) {
                case 2:
                    $output -> write('<comment>overwritten</comment>   ');
                    break;
                case 1:
                    $output -> write('<info>installed</info>     ');
                    break;
                default:
                    $output -> write('<error>ignored</error>       ');
                    break;
            }
            
            $output -> writeln(' '.$node);
        };
        
        $force    = (boolean) $input->getOption('force');
        $withDeps = (boolean) $input->getOption('deps');
        
        $packages = $mngr -> resolvePackages($input->getArgument('package'), $withDeps);
        
        $output -> write('Installing' . ($withDeps ? ' (extended list)' : '') .":\n   ");
        
        foreach ($packages as $package) {
            $output -> write('<info>' . $package -> getName() . '</info> ');
        }
        
        $output -> writeln('');
        
        foreach ($packages as $package) {
            $output -> writeln('Package <comment>'.$package->getName().'</comment>:');
            $mngr -> install($package, $force, $listener);
        }
    }
}

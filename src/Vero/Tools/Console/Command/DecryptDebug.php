<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Tools\Console\Command;

use Vero\Debug\CryptEnv;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console;

/**
 * Console vero:compact-libs command.
 */
class DecryptDebug extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            -> setName('vero:decrypt-debug')
            -> setDescription('Decrypt message showed to user by Vero\Debug\CryptEnv on critical error in production mode.')
            -> addArgument('message', InputArgument::OPTIONAL, 'The path to file with encoded message or string with message', 'debug.txt')
            -> addOption('key', 'k', InputOption::VALUE_OPTIONAL, 'Path to private key file', 'key.pem')
            -> setHelp(
<<<EOT
Decrypt message showed to user by Vero\Debug\CryptEnv on critical error in production mode.

Encrypted message should be base64 encoded string with serialized two elements array (evelope and encrypted data).
OpenSSL extension is required.
EOT
            );
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $key = $input -> getOption('key');
        $message = $input -> getArgument('message');
        
        if (file_exists($key)) {
            $key = 'file://'.$key;
        }
        
        if (file_exists($message)) {
            $message = file_get_contents($message);
        }
        
        $output -> writeln(CryptEnv::decrypt($message, $key));
    }
}

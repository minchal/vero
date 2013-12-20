<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Tools\Console;

use Symfony\Component\Console\Application;

/**
 * Helper for adding commands to symfony console application.
 */
class ConsoleRunner
{
    /**
     * Add Vero standard commands.
     */
    public static function addCommands(Application $app)
    {
        $app -> addCommands(
            [
                new Command\DatabaseExport(),
                new Command\CompactLibs(),
                new Command\DecryptDebug(),
                new Command\PackagesInstall(),
                new Command\PackagesUninstall(),
                new Command\PackagesStatus(),
            ]
        );
    }
}

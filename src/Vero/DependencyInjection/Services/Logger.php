<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\DependencyInjection\Services;

use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\LazyService;
use Vero\Log;

class Logger extends LazyService
{
    protected function create(Container $c)
    {
        return new Log\Logger(
            $c -> get('config') -> get('log.level', 'info'),
            new Log\Backend\File(
                $c -> get('app') -> path('var/log/'),
                $c -> get('config') -> get('log.maxSize', Log\Backend\File::K128)
            )
        );
    }
}

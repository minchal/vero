<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\DependencyInjection\Services;

use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\LazyService;
use Vero\Cache\Cache as C;
use Doctrine\Common\Cache\FilesystemCache;

class Cache extends LazyService
{
    protected function create(Container $c)
    {
        return new C(
            new FilesystemCache(
                $c -> get('app') -> path('var/cache/')
            )
        );
    }
}

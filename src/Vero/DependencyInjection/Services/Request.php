<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\DependencyInjection\Services;

use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\LazyService;
use Vero\Web\Request as R;

class Request extends LazyService
{
    protected function create(Container $c)
    {
        return R::createFromGlobals();
    }
}

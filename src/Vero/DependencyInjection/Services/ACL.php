<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\DependencyInjection\Services;

use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\LazyService;
use Vero\ACL as A;

class ACL extends LazyService
{
    protected function create(Container $c)
    {
        $acl = new A\ACL(
            new A\Backend\XML(
                $c -> get('app') -> path('resources/acl/'),
                $c -> get('cache')
            )
        );

        if ($c -> has('auth')) {
            $acl -> setSessionRole($c -> get('auth') -> getUser());
        }

        return $acl;
    }
}

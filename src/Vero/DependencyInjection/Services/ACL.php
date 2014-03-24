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
        if ($c -> has('acl-backend')) {
            $backend = $c -> get('acl-backend');
        } else {
            $backend = new A\Backend\XML(
                $c -> get('app') -> path('resources/acl/'),
                $c -> get('cache')
            );
        }
        
        $acl = new A\ACL($backend);

        if ($c -> has('auth')) {
            $acl -> setSessionRole($c -> get('auth') -> getUser());
        }

        return $acl;
    }
}

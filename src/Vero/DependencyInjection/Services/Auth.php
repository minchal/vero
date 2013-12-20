<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\DependencyInjection\Services;

use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\LazyService;
use Vero\Web\Auth\Manager;

class Auth extends LazyService
{
    protected function create(Container $c)
    {
        $auth = new Manager(
            $c -> get('session'),
            $c -> get('auth-provider')
        );

        $request = $c -> get('request');

        $c -> get('controller') -> addSendListener(function ($response) use ($request, $auth) {
            $auth -> visit($request);
        });

        if ($auth -> usesAutologin()) {
            $auth -> addLoadListener(function ($user) use ($request, $auth) {
                if ($user -> isGuest()) {
                    $auth -> autologin($request);
                }
            });
        }

        return $auth;
    }
}

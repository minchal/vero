<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\DependencyInjection\Services;

use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\LazyService;
use Vero\Routing;

class Router extends LazyService
{
    protected function create(Container $c)
    {
        $config = $c -> get('config');

        $prefix = $config -> get('routing.prefix');

        if ($c -> has('request')) {
            $req = $c -> get('request');
            $domain = $config -> get('routing.domain', $req -> host());
            $scheme = $config -> get('routing.scheme', $req -> scheme());
            $base = $config -> get('routing.base', $req -> guessBase());
        } else {
            $domain = $config -> get('routing.domain');
            $scheme = $config -> get('routing.scheme');
            $base = $config -> get('routing.base', '/');
        }

        $router = new Routing\Router($base, $prefix, $domain, $scheme);

        $builder = new Routing\Builder\XML(
            $c -> get('app') -> path('resources/routes/'),
            $c -> get('cache')
        );

        return $builder -> fill($router);
    }
}

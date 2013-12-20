<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\DependencyInjection\Services;

use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\LazyService;
use Vero\Web\Session as S;

class Session extends LazyService
{
    protected function create(Container $c)
    {
        $config = $c -> get('config');
        
        switch ($b = $config -> get('session.backend', 'file')) {
            case 'database':
                $backend = new S\Backend\Database(
                    $c -> get('doctrine') -> getConnection(),
                    $config -> get('database.prefix').'session'
                );
                break;
            case 'file':
                $backend = new S\Backend\File($c -> get('app') -> path('var/session/'));
                break;
            default:
                throw new \RuntimeException("Session backend '$b' not recognized!");
        }
        
        $session = new S\Session($backend, $config -> get('session', []));
        $session -> start($c -> get('request'));

        $c -> get('controller') -> addSendListener(array($session, 'close'));

        return $session;
    }
}

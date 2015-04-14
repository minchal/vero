<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web;

use Vero\DependencyInjection\Container;
use Vero\Web\Request;
use Vero\Helper\Shortcut;

/**
 * Basic web action.
 */
abstract class Action implements ActionInterface
{
    use Shortcut\DITrait,
        Shortcut\ResponseTrait,
        Shortcut\RouterTrait,
        Shortcut\I18nTrait,
        Shortcut\UITrait,
        Shortcut\DoctrineTrait,
        Shortcut\EventDispatcherTrait,
        Shortcut\SessionTrait,
        Shortcut\UserTrait,
        Shortcut\ExceptionsTrait;
    
    /**
     * @var \Vero\Web\Controller
     */
    protected $controller;
    
    /**
     * @var \Vero\Routing\Router
     */
    protected $router;
    
    /**
     * {@inheritdoc}
     */
    public function __construct(Container $container)
    {
        $this -> setContainer($container);
        
        $this -> controller = $this -> get('controller');
        $this -> router     = $this -> get('router');
        
        $this -> init();
    }
    
    /**
     * Initialization procedures.
     * 
     * @api
     */
    protected function init()
    {
        $this -> setDomain();
    }
    
    /**
     * Default throw Not Found exception.
     * 
     * {@inheritdoc}
     */
    public function run(Request $request)
    {
        throw new Exception\NotFound();
    }
    
    /**
     * Forward request to another action.
     * 
     * @param string
     * @return mixed
     */
    public function forward($routeId, array $params = [])
    {
        $class = $this -> router -> getRoute($routeId) -> getAction();
        $action = new $class($this -> getContainer());
        
        return $action -> run($this -> get('request') -> setParams($params));
    }
}

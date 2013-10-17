<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Application;

use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\InstanceService;

/**
 * Abstract controller.
 */
abstract class Controller
{
    protected $container;
    
    /**
     * Construct controller with Container.
     * 
     * @param \Vero\DependencyInjection\Container
     */
    public function __construct(Container $container)
    {
        $this -> container = $container;
        $this -> container -> set('controller', new InstanceService($this));
    }
    
    /**
     * Run controller.
     */
    abstract public function run();
}

<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Helper\Shortcut;

use Vero\DependencyInjection\Container;

/**
 * Shortcuts to use Dependency Injection Container.
 * 
 * Default implementation for Vero\DependencyInjection\Depenedent interface.
 */
trait DITrait
{
    /**
     * @var Container
     */
    protected $container;
    
    /**
     * Set DIC instance.
     * 
     * Compatible with Vero\DependencyInjection\Depenedent interface.
     * 
     * @see \Vero\DependencyInjection\Depenedent
     * @return self
     */
    public function setContainer(Container $container)
    {
        $this -> container = $container;
        
        return $this;
    }
    
    /**
     * Get current DIC instance.
     * 
     * @return Container
     */
    public function getContainer()
    {
        return $this -> container;
    }
    
    /**
     * Get instance of service from DI Container.
     * 
     * @see Vero\DependencyInjection\Container::get()
     * @param string $name
     * @param mixed $args
     * @return mixed
     */
    public function get($name, $args = [])
    {
        return $this -> container -> get($name, (array) $args);
    }
}

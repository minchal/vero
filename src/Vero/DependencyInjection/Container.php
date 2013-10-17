<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\DependencyInjection;

/**
 * Simple Dependency Injection Container.
 */
class Container
{
    /**
     * Set service for ID.
     * 
     * @param string
     * @param Service
     * @return self
     */
    public function set($id, Service $service)
    {
        $service -> setContainer($this);
        $this -> services[$id] = $service;
        
        return $this;
    }
    
    /**
     * Is gived service registered?
     * 
     * @param string
     * @return boolean
     */
    public function has($id)
    {
        return isset($this -> services[$id]);
    }
    
    /**
     * Retrive instance of registered service.
     * 
     * @param string
     * @param mixed Optional arguments for service's get() method
     * @return mixed
     */
    public function get($id, $args = [])
    {
        if (!isset($this -> services[$id])) {
            throw new \OutOfRangeException('Service '.$id.' is not registered.');
        }
        
        return call_user_func_array(array($this -> services[$id], 'get'), (array) $args);
    }
}

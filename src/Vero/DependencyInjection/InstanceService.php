<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\DependencyInjection;

/**
 * Service simply holds instance of any type.
 */
class InstanceService implements Service
{
    protected $instance;
    
    /**
     * Service is already created.
     * 
     * @param mixed
     */
    public function __construct($instance)
    {
        $this -> instance = $instance;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setContainer(Container $container)
    {
        
    }
    
    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this -> instance;
    }
}

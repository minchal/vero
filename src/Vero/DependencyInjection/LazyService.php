<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\DependencyInjection;

/**
 * Abstract service for objects loaded and created when used first time.
 */
abstract class LazyService implements Service
{
    protected $instance;
    protected $container;
    
    /**
     * {@inheritdoc}
     */
    public function setContainer(Container $container)
    {
        $this -> container = $container;
    }
    
    /**
     * {@inheritdoc}
     */
    public function get()
    {
        if (!$this -> instance) {
            if (!$this -> container) {
                throw new \LogicException('Before calling get() of Lazy service, you must set Container instance.');
            }
            
            $this -> instance = $this -> create($this -> container);
        }
        
        return $this -> instance;
    }
    
    /**
     * Create service when first used.
     * 
     * @return mixed
     */
    abstract protected function create(Container $container);
}

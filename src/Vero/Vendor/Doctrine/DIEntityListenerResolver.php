<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Doctrine;

use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\Dependent;
use Doctrine\ORM\Mapping\DefaultEntityListenerResolver;

class DIEntityListenerResolver extends DefaultEntityListenerResolver
{
    protected $container;
    
    public function __construct(Container $container)
    {
        $this -> container = $container;
    }
    
    public function resolve($className)
    {
        if (!isset($this -> instances[$className = trim($className, '\\')])) {
            $this -> instances[$className] = $this -> create($className);
        }

        return $this -> instances[$className];
    }
    
    protected function create($className)
    {
        $call = [$className,'getDependencyInjectionKey'];
        
        if (is_callable($call) && $this -> container -> has($call())) {
            return $this -> container -> get($call());
        }

        $listener = new $className();
        
        if ($listener instanceof Dependent) {
            $listener -> setContainer($this -> container);
        }
        
        return $listener;
    }
}

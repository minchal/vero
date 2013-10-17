<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\View\Widget;

use Vero\DependencyInjection\Container;

/**
 * Abstract Widget holds only DI Container.
 */
abstract class Widget
{
    protected $container;
    
    /**
     * All widgets have access do DI Container.
     */
    public function __construct(Container $container)
    {
        $this -> container = $container;
        $this -> init();
    }
    
    protected function init()
    {
        
    }
}

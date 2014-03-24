<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\View\Widget;

use Vero\DependencyInjection\Container;
use Vero\Helper\Shortcut;

/**
 * Abstract Widget holds only DI Container.
 */
abstract class Widget
{
    use Shortcut\DITrait,
        Shortcut\I18nTrait;
    
    /**
     * All widgets have access do DI Container.
     */
    public function __construct(Container $container)
    {
        $this -> setContainer($container);
        
        $this -> init();
    }
    
    protected function init()
    {
        
    }
}

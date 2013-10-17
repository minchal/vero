<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web;

use Vero\DependencyInjection\Container;

/**
 * Abstract controller.
 */
interface ActionInterface
{
    /**
     * Construct action with Container.
     * 
     * @param \Vero\DependencyInjection\Container
     */
    public function __construct(Container $container);
    
    /**
     * Run action with Request and Response instances.
     * 
     * @return Vero\Web\Response|Vero\Web\RsponseBody|string
     */
    public function run(Request $request);
}

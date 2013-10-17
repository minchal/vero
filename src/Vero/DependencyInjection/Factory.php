<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\DependencyInjection;

/**
 * Abstract service working as factory.
 */
abstract class Factory implements Service
{
    protected $container;
    
    /**
     * {@inheritdoc}
     */
    public function setContainer(Container $container)
    {
        $this -> container = $container;
    }
}

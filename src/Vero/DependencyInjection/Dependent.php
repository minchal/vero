<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\DependencyInjection;

/**
 * Class, which is dependend from DI Container.
 */
interface Dependent
{
    /**
     * Set DI Container.
     */
    public function setContainer(Container $container);
}

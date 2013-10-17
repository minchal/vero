<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\DependencyInjection;

/**
 * Service container.
 */
interface Service extends Dependent
{
    /**
     * Retrive instance of service.
     * 
     * @return mixed
     */
    public function get();
}

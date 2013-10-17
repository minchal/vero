<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Config;

/**
 * Static array of configuration.
 */
class StaticArray extends Config
{
    /**
     * Construct instance with config array.
     */
    public function __construct(array $config)
    {
        $this -> config = $config;
    }
}

<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule\Set;

/**
 * Set of values validable by validator.
 */
interface SetInterface
{
    /**
     * Check, if key is valid in this set.
     * 
     * @param mixed $key
     * @returns boolean
     */
    public function has($key);
    
    /**
     * Get value for key.
     * 
     * @param mixed $key
     * @returns mixed
     */
    public function value($key);
}

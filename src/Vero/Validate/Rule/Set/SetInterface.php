<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule\Set;

/**
 * Interface to validate key in set and to retrive value.
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
    
    /**
     * Get key from value.
     * 
     * @param mixed $item
     * @returns mixed
     */
    public function getKey($item);
    
    /**
     * Get keys from array value.
     * 
     * @param array $items
     * @returns array
     */
    public function getKeys($items);
}

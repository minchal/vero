<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule\Set;

/**
 * Set with additional displaying ability.
 */
interface ShowableSetInterface extends SetInterface
{
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
    
    /**
     * Get key from value.
     * 
     * @param mixed $item
     * @returns string
     */
    public function getDesc($item);
    
    /**
     * Get array of desc from values.
     * 
     * @param array $items
     * @returns array
     */
    public function getDescs($items);
}

<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

use Vero\Validate\BasicRule;

/**
 * Test, if value exists as key in array.
 * 
 * If option 'multi' is true, value is treated as array.
 * 
 * Options:
 *  - optional (default: false)
 *  - items (array)
 */
class Set extends BasicRule
{
    /**
     * {@inheritdoc}
     */
    public function test(&$value, array $options = [])
    {
        $value = (string) $this -> getScalar($value);
        
        if (!$value) {
            $value = null;
            return $this -> testRequired($value, $options);
        }
        
        $items = $this -> option($options, 'items', []);
        
        if (!$items instanceof Set\SetInterface) {
            $items = new Set\ArraySet($items);
        }
        
        if (!$items -> has($value)) {
            $this -> optionalError($options, 'set');
            return false;
        }
        
        $value = $items -> value($value);
        
        return true;
    }
}

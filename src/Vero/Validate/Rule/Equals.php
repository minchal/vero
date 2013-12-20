<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

use Vero\Validate\BasicRule;

/**
 * Value equals rule.
 * Value must equals with speciefied value.
 * 
 * Options:
 *  - optional (default: false)
 *  - value
 */
class Equals extends BasicRule
{
    /**
     * {@inheritdoc}
     */
    public function test(&$value, array $options = [])
    {
        $value = $this -> getScalar($value);
        
        if (!$value) {
            $value = null;
            return $this -> testRequired($value, $options);
        }
        
        if ($value != $this -> option($options, 'value')) {
            $this -> error('equals');
            return false;
        }
        
        return true;
    }
}

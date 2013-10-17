<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

/**
 * Number without fractional part.
 * 
 * Options:
 *  - optional (default: false)
 *  - min (default: 0, set to null to remove)
 *  - max
 */
class Integer extends Number
{
    /**
     * {@inheritdoc}
     */
    public function test(&$value, array $options = [])
    {
        $value = $this -> getScalar($value);
        
        if (!$value) {
            $value = 0;
            return $this -> testRequired($value, $options);
        }
        
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            $this -> error('integer');
            return false;
        }
        
        $value = (int) $value;
        
        return $this -> testRange($value, $options);
    }
}

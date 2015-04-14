<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

/**
 * Numstr rule.
 * Value must contain numbers and '-'.
 * 
 * Options:
 *  - optional (default: false)
 *  - min
 *  - max (default: 20)
 *  - length
 */
class Numstr extends String
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
        
        $options['max'] = $this -> option($options, 'max', 20);
        
        if (!$this -> testLength($value, $options)) {
            return false;
        }
        
        if (!preg_match('/^[-0-9]+$/', $value)) {
            $this -> optionalError($options, 'numstr');
            return false;
        }
        
        return true;
    }
}

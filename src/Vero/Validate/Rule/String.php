<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

use Vero\Validate\BasicRule;

/**
 * String rule. 
 * 
 * Options:
 *  - optional (default: false)
 *  - min
 *  - max
 *  - length
 */
class String extends BasicRule
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
        
        return $this -> testLength($value, $options);
    }
    
    protected function testLength(&$value, $options)
    {
        $len = mb_strlen($value);
        
        if (isset($options['length'])) {
            if ($options['length'] != $len) {
                return $this -> error('length compare', $options['length']);
            }
        }
        
        $min = $this -> option($options, 'min');
        $max = $this -> option($options, 'max');
        
        if ($min !== null && $max !== null) {
            if ($len < $min || $len > $max) {
                return $this -> error('length scope', [$min, $max]);
            }
        } elseif ($min !== null) {
            if ($len < $min) {
                return $this -> error('length min', $min);
            }
        } elseif ($max !== null) {
            if ($len > $max) {
                return $this -> error('length max', $max);
            }
        }
        
        return true;
    }
}

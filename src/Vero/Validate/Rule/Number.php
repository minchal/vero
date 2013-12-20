<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

use Vero\Validate\BasicRule;

/**
 * Number rule. 
 * 
 * Options:
 *  - optional (default: false)
 *  - min (default: 0, set to null to remove)
 *  - max
 *  - precision (default: 2)
 *  - nullable (boolean, default: false) - Transform empty string value to null (instead of 0)
 */
class Number extends BasicRule
{
    /**
     * {@inheritdoc}
     */
    public function test(&$value, array $options = [])
    {
        $value = $this -> getScalar($value);
        
        if (!$value) {
            if ($this -> option($options, 'nullable') && $value === '') {
                $value = null;
            } else {
                $value = 0;
            }
            
            return $this -> testRequired($value, $options);
        }
        
        if (!preg_match('/^(\-)?[0-9]+[\.,]?[0-9]*$/', $value)) {
            $this -> error('number');
            return false;
        }
        
        if (!$this -> testPrecision($value, $options)) {
            return false;
        }
        
        $value = (float) str_replace(',', '.', $value);
        
        return $this -> testRange($value, $options);
    }
    
    protected function testRange(&$value, $options)
    {
        $min = $this -> option($options, 'min', 0);
        $max = $this -> option($options, 'max');
        
        if ($min !== null && $max !== null) {
            if ($value < $min || $value > $max) {
                return $this -> error('range scope', [$min, $max]);
            }
        } elseif ($min !== null) {
            if ($value < $min) {
                return $this -> error('range min', $min);
            }
        } elseif ($max !== null) {
            if ($value > $max) {
                return $this -> error('range max', $max);
            }
        }
        
        return true;
    }
    
    protected function testPrecision(&$value, $options)
    {
        $prec = $this -> option($options, 'precision', 2);
        
        $regexp = '/^(\-)?[0-9]+[\.,]?[0-9]{0,'.$prec.'}$/';
        
        if (!preg_match($regexp, $value)) {
            $this -> error('precision', [0, $prec]);
            return false;
        }
        
        return true;
    }
}

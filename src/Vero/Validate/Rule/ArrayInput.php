<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

use Vero\Validate\BasicRule;

/**
 * Validate array.
 * All items in array must fulfill next Rule (with corresponding options).
 * 
 * Options:
 *  - optional (default: false)
 *  - rule (string, default: null)
 *  - options (array)
 */
class ArrayInput extends BasicRule
{
    const SEPARATOR = ',';
    
    /**
     * {@inheritdoc}
     */
    public function test(&$value, array $options = [])
    {
        if ($value && !is_array($value)) {
            $value = explode(self::SEPARATOR, (string) $this -> getScalar($value));
        }
        
        if (!$value) {
            $value = [];
            return $this -> testRequired($value, $options);
        }
        
        $rule = $this -> option($options, 'rule');
        $opts = $this -> option($options, 'options', []);
        
        // is array, not empty, but without filters
        if (!$rule) {
            return true;
        }
        
        $rule = $this -> validator -> getRule($rule);
        
        foreach ($value as &$v) {
            if (!$rule -> test($v, $opts)) {
                $this -> lastError = $rule -> getLastError();
                return false;
            }
        }
        
        return true;
    }
}

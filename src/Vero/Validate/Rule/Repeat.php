<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

use Vero\Validate\BasicRule;

/**
 * Field repeat rule.
 * Value must equals with other field's value, defined in validator.
 * 
 * Options:
 *  - optional (default: false)
 *  - field (default: 'password')
 */
class Repeat extends BasicRule
{
    /**
     * {@inheritdoc}
     */
    public function test(&$value, array $options = [])
    {
        $value2 = $this -> validator -> value($this -> option($options, 'field', 'password'));
        
        if (!$value && !$value2) {
            $value = null;
            return $this -> testRequired($value, $options);
        }
        
        if ($value != $value2) {
            $this -> error('repeat');
            return false;
        }
        
        return true;
    }
}

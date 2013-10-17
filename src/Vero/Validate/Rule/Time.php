<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

use Vero\Validate\BasicRule;

/**
 * Time rule.
 * Value must be in format hh:mm:ss.
 * 
 * Options:
 *  - optional (default: false)
 */
class Time extends BasicRule
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
        
        if (!preg_match('/^[0-9]{2}:[0-9]{2}:[0-9]{2}?$/', $value)) {
            $this -> error('time');
            return false;
        }
        
        return true;
    }
}

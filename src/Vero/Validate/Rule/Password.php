<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

/**
 * Password rule.
 * Value can contain only ASCII chars.
 * 
 * Options:
 *  - optional (default: false)
 *  - min (default: 5)
 *  - max (default: 100)
 *  - length
 */
class Password extends String
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
        
        $options['min'] = $this -> option($options, 'min', 5);
        $options['max'] = $this -> option($options, 'max', 100);
        
        if (!$this -> testLength($value, $options)) {
            return false;
        }
        
        if (!preg_match('/^[ -~]+$/', $value)) {
            $this -> error('password');
            return false;
        }
        
        return true;
    }
}

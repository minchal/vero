<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

/**
 * E-mail address rule.
 * String with FILTER_VALIDATE_EMAIL test.
 * 
 * Options:
 *  - optional (default: false)
 *  - min
 *  - max (default: 100)
 *  - length
 */
class Email extends String
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
        
        $options['max'] = $this -> option($options, 'max', 100);
        
        if (!$this -> testLength($value, $options)) {
            return false;
        }
        
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this -> error('email');
            return false;
        }
        
        return true;
    }
}

<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

/**
 * URL rule.
 * String with FILTER_VALIDATE_URL test.
 * 
 * Options:
 *  - optional (default: false)
 *  - min
 *  - max (default: 250)
 *  - length
 */
class Url extends String
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
        
        $options['max'] = $this -> option($options, 'max', 250);
        
        if (!$this -> testLength($value, $options)) {
            return false;
        }
        
        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            $this -> error('url');
            return false;
        }
        
        return true;
    }
}

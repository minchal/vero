<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

/**
 * Time rule.
 * Value must be in format hh:mm:ss.
 * 
 * Options:
 *  - optional (default: false)
 *  - min (\DateTime object)
 *  - max (\DateTime object)
 *  - format (only for min and max options, default: 'H:i:s')
 */
class Time extends DateTime
{
    const FORMAT = 'H:i:s';
    
    /**
     * {@inheritdoc}
     */
    public function test(&$value, array $options = [])
    {
        if (!is_scalar($value)) {
            $value = null;
        }
        
        if (!$value) {
            $value = null;
            return $this -> testRequired($value, $options);
        }
        
        try {
            $value = new \DateTime($value);
            $value ->setDate(1970, 1, 1);
        } catch (\Exception $e) {
            $this -> optionalError($options, 'time');
            return false;
        }
        
        return $this -> testRange($value, $options);
    }
}

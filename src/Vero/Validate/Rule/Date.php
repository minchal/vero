<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

/**
 * Date rule.
 * Same as DateTime Rule, but returned object always have 00:00:00 time part.
 * 
 * Options:
 *  - optional (default: false)
 *  - min (\DateTime object)
 *  - max (\DateTime object)
 *  - format (only for min and max options, default: 'Y-m-d')
 * 
 * @see DateTime
 */
class Date extends DateTime
{
    const FORMAT = 'Y-m-d';
    
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
            $value -> setTime(0, 0, 0);
        } catch (\Exception $e) {
            $this -> optionalError($options, 'date');
            return false;
        }
        
        return $this -> testRange($value, $options);
    }
}

<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

use Vero\Validate\BasicRule;

/**
 * DateTime rule.
 * Try to parse any string as \DateTime
 * 
 * *This Rule transforms $value to \DateTime object!*
 * 
 * Options:
 *  - optional (default: false)
 *  - min (\DateTime object)
 *  - max (\DateTime object)
 *  - format (only for min and max options, default: 'Y-m-d H:i:s')
 * 
 * @see \DateTime
 * @see \strtotime()
 */
class DateTime extends BasicRule
{
    const FORMAT = 'Y-m-d H:i:s';
    
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
        } catch (\Exception $e) {
            $this -> optionalError($options, 'datetime');
            return false;
        }
        
        return $this -> testRange($value, $options);
    }

    protected function testRange(\DateTime $value, $options)
    {
        $min = $this -> option($options, 'min');
        $max = $this -> option($options, 'max');
        $format = $this -> option($options, 'format', self::FORMAT);
        
        if ($min !== null && $max !== null) {
            if ($value < $min || $time > $max) {
                return $this->optionalError($options, 'range scope', ['min'=>$min->format($format), 'max'=>$max->format(self::$format)]);
            }
        } elseif ($min !== null) {
            if ($value < $min) {
                return $this->optionalError($options, 'range min', ['min'=>$min->format($format)]);
            }
        } elseif ($max !== null) {
            if ($value > $max) {
                return $this->optionalError($options, 'range max', ['max'=>$max->format($format)]);
            }
        }
        
        return true;
    }
}

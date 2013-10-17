<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

use Vero\Validate\BasicRule;

/**
 * Regexp rule.
 * This is universal rule for regular expressions.
 * Error message can be parametrized with description other than regexp pattern.
 * 
 * Option:
 *  - pattern (ex. '/^[0-9]{2}-[0-9]{3}$/')
 *  - format (ex. '##-###')
 */
class Regexp extends BasicRule
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
        
        if (!preg_match($this -> option($options, 'pattern'), $value)) {
            $this -> error(
                'regexp',
                ['format' => $this -> option($options, 'format', $this -> option($options, 'pattern'))]
            );
            
            return false;
        }
        
        return true;
    }
}

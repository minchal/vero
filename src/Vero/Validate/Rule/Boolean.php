<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

use Vero\Validate\BasicRule;

/**
 * Boolean rule (for checkbox etc.)
 * 
 * Options:
 *  - optional (default: false)
 */
class Boolean extends BasicRule
{
    /**
     * {@inheritdoc}
     */
    public function test(&$value, array $options = [])
    {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        
        if (!$value) {
            return $this -> testRequired($value, $options);
        }
        
        return true;
    }
}

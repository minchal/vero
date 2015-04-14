<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

use Vero\Validate\BasicRule;

/**
 * Callback rule. 
 * 
 * Options:
 *  - optional (default: false)
 *  - callback
 */
class Callback extends BasicRule
{
    /**
     * {@inheritdoc}
     */
    public function test(&$value, array $options = [])
    {
        if (!$value) {
            $value = null;
            return $this -> testRequired($value, $options);
        }
        
        $callback = $this -> option($options, 'callback', function() {
            return true;
        });
        
        if (!$callback($value)) {
            return $this -> optionalError($options, 'callback');
        }
        
        return true;
    }
}

<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

use Vero\Validate\BasicRule;

/**
 * Special rule to validate multiple rules on one field.
 * 
 * Options: array with other rules and options, ex:
 * 
 * ['idstr', ['repeat',['field'=>'other']] ]
 */
class Chain extends BasicRule
{
    /**
     * {@inheritdoc}
     */
    public function test(&$value, array $options = [])
    {
        foreach ($options as $row) {
            if (is_array($row)) {
                list($rule, $opts) = $row;
            } else {
                $rule = $row;
                $opts = [];
            }
            
            $rule = $this -> validator -> getRule($rule);
            
            if (!$rule -> test($value, $opts)) {
                $this -> lastError = $rule -> getLastError();
                return false;
            }
        }
        
        return true;
    }
}

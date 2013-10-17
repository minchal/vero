<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule;

/**
 * Idstr rule.
 * Value must contain only letters, numbers, '-' and '_'.
 * Value must begin with letter.
 * 
 * Options:
 *  - optional (default: false)
 *  - min
 *  - max (default: 100)
 *  - length
 */
class Idstr extends String
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
        
        if (!preg_match('/^[-_a-zA-Z0-9]+$/', $value)) {
            $this -> error('idstr');
            return false;
        }
        if (!preg_match('/^([a-zA-Z]+)(.*)$/', $value)) {
            $this -> error('idstr letter first');
            return false;
        }
        
        return true;
    }
}

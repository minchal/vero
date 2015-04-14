<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate;

/**
 * Basic abstract implementation of rule.
 * First, check if value is required/optional
 */
abstract class BasicRule implements Rule
{
    protected $lastError;
    protected $validator;
    
    /**
     * {@inheritdoc}
     */
    public function setValidator(Validator $validator)
    {
        $this -> validator = $validator;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getLastError()
    {
        return $this -> lastError;
    }
    
    /**
     * Set internal error message with optional custom message.
     */
    protected function optionalError($options, $id, $args = [], $domain = null)
    {
        if (isset($options['errors'][$id])) {
            $this -> lastError = Error::raw($options['errors'][$id]);
        } else {
            $this -> error($id, $args, $domain);
        }
    }
    
    /**
     * Set internal error message.
     */
    protected function error($id, $args = [], $domain = null)
    {
        $this -> lastError = Error::create($id, $args, $domain);
    }
    
    /**
     * Get option or default value.
     */
    protected function option($options, $name, $default = null)
    {
        return array_key_exists($name, $options) ? $options[$name] : $default;
    }
    
    /**
     * {@inheritdoc}
     */
    abstract public function test(&$value, array $options = []);
    
    /**
     * Test for empty variables.
     * If variable is optional, return true.
     */
    protected function testRequired(&$value, $options)
    {
        if ($this -> option($options, 'optional')) {
            return true;
        } else {
            $this -> optionalError($options, 'required');
            return false;
        }
    }
    
    /**
     * Safe check, that value is scalar.
     */
    protected function getScalar(&$value)
    {
        if (!is_scalar($value)) {
            return null;
        }
        
        return $value;
    }
}

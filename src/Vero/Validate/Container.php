<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate;

/**
 * Abstract implementation of VFC, that holds fields as internal array.
 */
abstract class Container implements ContainerInterface, RemotableContainerInterface
{
    /**
     * Associative array:
     * 
     * 'field1' => ['ruleA', [option1 => '5', option2 => '10']],
     * 'field2' => ['ruleB', [optional => true]],
     * 'field3' => ['chain', ['ruleC', ['ruleA',['option'=>'value']]]],
     * 
     * @var array
     */
    protected $fields = [];
    
    /**
     * Array with filds available to validate from AJAX request.
     * 
     * 'field1' => true
     * 'field2' => false
     * 
     * @var array
     */
    protected $remotes = [];
    
    /**
     * Set rule for field
     * 
     * @param string
     * @param mixed
     * @param array
     * @return self
     */
    public function set($field, $rule, array $options = [])
    {
        $this -> fields[$field] = [$rule, $options];
        return $this;
    }
    
    /**
     * Add rule to field.
     * If field has rule, create chain rule.
     * 
     * @param string
     * @param mixed
     * @param array
     * @return self
     */
    public function add($field, $rule, array $options = [])
    {
        if (!isset($this -> fields[$field])) {
            return $this -> set($field, $rule, $options);
        }
        
        if ($this -> fields[$field][0] != 'chain') {
            $t = $this -> fields[$field];
            $this -> fields[$field] = ['chain', [$t]];
        }
        
        $this -> fields[$field][1][] = [$rule, $options];
        
        return $this;
    }
    
    /**
     * Shortcut for 'array' rule.
     * 
     * @param string
     * @param string
     * @param array
     * @param boolean
     * @return self
     */
    public function addArray($field, $rule, array $options = [], $optional = false)
    {
        $this -> add($field, 'array', ['rule' => $rule, 'options' => $options, 'optional' => $optional]);
        return $this;
    }
    
    /**
     * Set option for field, that is not already chained.
     * 
     * @param string
     * @param string
     * @param mixed
     * @return self
     */
    public function setOption($field, $option, $value)
    {
        if (isset($this -> fields[$field][0]) && $this -> fields[$field][0] == 'chain') {
            throw new \BadMethodCallException('Method setOption() does not work with chain rules!');
        }
        
        $this -> fields[$field][1][$option] = $value;
        return $this;
    }
    
    /**
     * Set option for field, that is not already chained.
     * 
     * @param string
     * @param string
     * @param mixed
     * @return self
     */
    public function setRemote($field, $value = true)
    {
        $this -> remote[$field] = $value;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return array_keys($this -> fields);
    }
    
    /**
     * {@inheritdoc}
     */
    public function exists($field)
    {
        return isset($this -> fields[$field]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function rule($field)
    {
        if (!isset($this -> fields[$field][0])) {
            throw new \OutOfRangeException('Field '.$field.' not found in Validator Field Container.');
        }
        
        return $this -> fields[$field][0];
    }
    
    /**
     * {@inheritdoc}
     */
    public function options($field)
    {
        return isset($this -> fields[$field][1]) ? $this -> fields[$field][1] : array();
    }
    
    /**
     * {@inheritdoc}
     */
    public function canRemoteCheck($field)
    {
        return isset($this -> remote[$field]) && $this -> remote[$field];
    }
}

<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate;

use IteratorAggregate;
use Countable;
use ArrayIterator;

/**
 * User input Validator.
 * Uses Rules and Field Containers (VFC).
 * Class can be used in chain:
 *    $validator -> map(...) -> mapContainer(...) -> isValid()
 */
class Validator implements IteratorAggregate, Countable
{
    /**
     * List of registered rules shortcuts/aliases.
     */
    protected $rules = [
        'chain'    => '\Vero\Validate\Rule\Chain',
        'callback' => '\Vero\Validate\Rule\Callback',
        'array'    => '\Vero\Validate\Rule\ArrayInput',
        'set'      => '\Vero\Validate\Rule\Set',
        
        'boolean'  => '\Vero\Validate\Rule\Boolean',
        'integer'  => '\Vero\Validate\Rule\Integer',
        'number'   => '\Vero\Validate\Rule\Number',
        
        'string'   => '\Vero\Validate\Rule\String',
        'email'    => '\Vero\Validate\Rule\Email',
        'url'      => '\Vero\Validate\Rule\Url',
        'idstr'    => '\Vero\Validate\Rule\Idstr',
        'numstr'   => '\Vero\Validate\Rule\Numstr',
        'password' => '\Vero\Validate\Rule\Password',
        'regexp'   => '\Vero\Validate\Rule\Regexp',
        
        'date'     => '\Vero\Validate\Rule\Date',
        'time'     => '\Vero\Validate\Rule\Time',
        'datetime' => '\Vero\Validate\Rule\DateTime',
        
        'repeat'   => '\Vero\Validate\Rule\Repeat',
        'equals'   => '\Vero\Validate\Rule\Equals',
    ];
    
    /**
     * Cached instances of rules.
     */
    protected $resolvedRules = [];

    /**
     * Raw registered input data.
     */
    protected $input = [];
    
    /**
     * Mapped fields rules.
     */
    protected $fields = [];
    
    /**
     * Mapped fields values.
     */
    protected $values = [];
    
    /**
     * Errors reported on mapping.
     */
    protected $errors = [];
    
    /**
     * Was this validator validated?
     * Resets when validator mapping are changed.
     * @var boolean
     */
    protected $validated = false;
    
    /**
     * Alias for constructor (for chain use).
     * 
     * @param array|ArrayAccess
     * @param \Vero\Validate\Container|null
     * @param array|null List of fields to map from $container
     * @return self
     * @see __construct()
     */
    public static function create($input = array(), ContainerInterface $container = null, $fields = null)
    {
        $validator = new self($input);
        
        if ($container) {
            $validator -> mapContainer($container, $fields);
        }
        
        return $validator;
    }
    
    /**
     * Construct validator instance with specified raw input data.
     * 
     * @param array|ArrayAccess
     */
    public function __construct($input = [])
    {
        if (!is_array($input) && !$input instanceof \ArrayAccess) {
            throw new \InvalidArgumentException('Validator input must be array or ArrayAccess instance.');
        }
        
        $this -> input = $input;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this -> values);
    }
    
    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this -> values);
    }
    
    /**
     * Alias for value().
     * 
     * @param string
     * @return mixed
     */
    public function __get($id)
    {
        return $this -> value($id);
    }
    
    /**
     * @param string
     * @return boolean
     */
    public function __isset($id)
    {
        return array_key_exists($id, $this -> values);
    }
    
    /**
     * Get all mapped values.
     * 
     * @return array
     */
    public function toArray()
    {
        return $this -> values;
    }
    
    /**
     * Get value of mapped field.
     * 
     * @param string
     * @return mixed
     */
    public function value($id)
    {
        if (!array_key_exists($id, $this -> values)) {
            throw new \OutOfRangeException('Speciefied key '.$id.' was not mapped by validator.');
        }
        
        return $this -> values[$id];
    }
    
    /**
     * Get error for specified field.
     * 
     * @param string
     * @return false|Error
     */
    public function error($id)
    {
        if (!$this -> isValidated()) {
            $this -> validate();
        }
        
        return isset($this -> errors[$id]) ? $this -> errors[$id] : false;
    }
    
    /**
     * Get all errors.
     * 
     * @return array
     */
    public function errors()
    {
        if (!$this -> isValidated()) {
            $this -> validate();
        }
        
        return $this -> errors;
    }
    
    /**
     * Return true, if in mapped fields was not any error.
     * 
     * @return boolean
     */
    public function isValid()
    {
        if (!$this -> isValidated()) {
            $this -> validate();
        }
        
        return empty($this -> errors);
    }
    
    /**
     * Return true, of Validator was fully validated.
     * 
     * @return boolean
     */
    public function isValidated()
    {
        return $this -> validated;
    }
    
    /**
     * Validate all mapped fields and return result.
     * 
     * @return boolean
     */
    public function validate()
    {
        foreach ($this->fields as $id => $field) {
            $rule = $this -> getRule($field['rule']);
            
            if (!$rule -> test($this -> values[$id], $field['options'])) {
                $this -> errors[$id] = $rule -> getLastError();
            }
        }
        
        $this -> validated = true;
        return empty($this -> errors);
    }
    
    /**
     * Set error for specified field.
     * 
     * @param string
     * @return self
     */
    public function setError($id, Error $error)
    {
        $this -> errors[$id] = $error;
        return $this;
    }
    
    /**
     * Get mapped field rule and options as array, ex.:
     *     rule => 'string'
     *     options => ['min'=>5]
     * 
     * @param string
     * @return array
     */
    public function getFieldRule($id)
    {
        return isset($this -> fields[$id]) ? $this -> fields[$id] : ['rule' => null, 'options' => []];
    }
    
    /**
     * Map specified field with Rule.
     * 
     * @param string
     * @param string|Rule
     * @param array Additional options for Rule
     * @return self
     * @see getRule()
     */
    public function map($id, $rule, array $options = [])
    {
        $this -> values[$id] = isset($this->input[$id]) ? $this->input[$id] : null;
        $this -> fields[$id] = ['rule' => $rule, 'options' => $options];
        $this -> validated = false;
        return $this;
    }
    
    /**
     * Map all or specified fields from Container.
     * Method can be used in chain.
     * 
     * @param Container
     * @param null|array
     * @return self
     */
    public function mapContainer(ContainerInterface $container, $fields = null)
    {
        if ($fields === null) {
            $fields = $container -> getAll();
        }
        
        foreach ($fields as $field) {
            $this -> map($field, $container -> rule($field), $container -> options($field));
        }
        
        return $this;
    }
    
    /**
     * Prepare instance of Rule.
     * 
     * @param string|Rule Rule object, rule index or class name
     * @return Rule
     */
    public function getRule($rule)
    {
        if ($rule instanceof Rule) {
            $rule -> setValidator($this);
            return $rule;
        }
        
        $name = $rule;
        
        if (!isset($this -> resolvedRules[$name])) {
            if (isset($this -> rules[$rule])) {
                $rule = $this -> rules[$rule];
            }
            
            if (!class_exists($rule)) {
                throw new \InvalidArgumentException('Validator rule "'.$rule.'" not recognized.');
            }
            
            $rule = new $rule();
            
            if (!$rule instanceof Rule) {
                throw new \InvalidArgumentException(
                    'Rule '.get_class($rule).' must be instance od \Vero\Validate\Rule.'
                );
            }
            
            $rule -> setValidator($this);
            
            $this -> resolvedRules[$name] = $rule;
        }
        
        return $this -> resolvedRules[$name];
    }
}

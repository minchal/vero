<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web\Session;

/**
 * Bag manages related variables and arrays holded in session.
 * 
 * Options:
 *  - max (maximum number of holded values)
 */
class Bag implements \IteratorAggregate, \Countable
{
    /**
     * @var array
     */
    protected $data = [];
    
    /**
     * @var array
     */
    protected $options = [];
    
    /**
     * Create Bag with options.
     */
    public function __construct(array $options = [])
    {
        $this -> options = array_merge($this->options, $options);
    }
    
    /**
     * Check if bag has speciefied value.
     * 
     * @param string
     * @return boolean
     */
    public function has($key)
    {
        return array_key_exists($key, $this -> data);
    }
    
    /**
     * Check if bag has speciefied key.
     * 
     * @param mixed
     * @return self
     */
    public function add($value)
    {
        $this -> data[] = $value;
        $this -> check();
        return $this;
    }
    
    /**
     * Get value from speciefied key.
     * 
     * @param string
     * @param mixed
     * @return self
     */
    public function set($key, $value)
    {
        $this -> data[$key] = $value;
        $this -> check();
        return $this;
    }
    
    /**
     * Get speciefied item.
     * 
     * @param string
     * @return mixed
     */
    public function get($key)
    {
        return isset($this -> data[$key]) ? $this -> data[$key] : null;
    }
    
    /**
     * Remove speciefied item and return its value.
     * 
     * @param string
     * @return mixed
     */
    public function delete($key)
    {
        $r = $this -> get($key);
        unset($this -> data[$key]);
        return $r;
    }
    
    /**
     * Clear bag and return all data.
     * 
     * @return array
     */
    public function clear()
    {
        $r = $this -> data;
        $this -> data = [];
        return $r;
    }
    
    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this -> data);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }
    
    /**
     * Check compliance with options.
     */
    protected function check()
    {
        if (isset($this->options['max']) && $this->options['max'] < count($this->data)) {
            $this -> data = array_slice($this->data, count($this->data) - $this->options['max']);
        }
    }
}

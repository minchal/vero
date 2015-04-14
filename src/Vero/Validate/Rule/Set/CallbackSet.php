<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule\Set;

/**
 * Set proxy for array of items and mapping 
 * functions to get key and value from item.
 */
class CallbackSet implements \Iterator, \Countable, ShowableSetInterface
{
    use ShowableSetTrait;
    
    protected $getKey;
    protected $getDesc;
    protected $getValue;
    protected $data;
    protected $position = 0;
    
    /**
     * Create Set.
     * 
     * @param array $data
     * @param callable $getKey
     * @param callable $getDesc
     * @param callable|null $getValue
     */
    public function __construct(array $data, callable $getKey, callable $getDesc, callable $getValue = null)
    {
        $this -> data    = $data;
        $this -> getKey  = $getKey;
        $this -> getDesc = $getDesc;
        $this -> getValue = $getValue ? $getValue : $getKey;
    }
    
    /**
     * {@inheritdoc}
     */
    public function value($key)
    {
        $getValue = $this -> getValue;
        
        foreach ($this -> data as $item) {
            if ($this -> getKey($item) == $key) {
                return $getValue($item);
            }
        }
        
        return null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        foreach ($this -> data as $item) {
            if ($this -> getKey($item) == $key) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getKey($item)
    {
        $getKey = $this -> getKey;
        return $getKey($item);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDesc($item)
    {
        $getDesc = $this -> getDesc;
        return $getDesc($item);
    }
    
    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this -> position = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        $fun = $this -> getDesc;
        return $fun($this -> data[$this -> position]);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        $fun = $this -> getKey;
        return $fun($this -> data[$this -> position]);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this -> position++;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return isset($this -> data[$this -> position]);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this -> data);
    }
}

<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule\Set;

/**
 * Common methods for ShowableSetInterface.
 */
trait ShowableSetTrait
{
    /**
     * {@inheritdoc}
     */
    abstract public function value($key);
    
    /**
     * {@inheritdoc}
     */
    public function valueAll($items)
    {
        $ret = [];
        
        if (!$items) {
            return $ret;
        }
        
        foreach ($items as $i) {
            if ($i) {
                $ret[] = $this -> value($i);
            }
        }
        
        return $ret;
    }
    
    /**
     * {@inheritdoc}
     */
    abstract public function getKey($item);
    
    /**
     * {@inheritdoc}
     */
    public function getKeys($items)
    {
        $ret = [];
        
        if (!$items) {
            return $ret;
        }
        
        foreach ($items as $i) {
            $ret[] = $this -> getKey($i);
        }
        
        return $ret;
    }
    
    /**
     * {@inheritdoc}
     */
    abstract public function getDesc($item);
    
    /**
     * {@inheritdoc}
     */
    public function getDescs($items)
    {
        $ret = [];
        
        if (!$items) {
            return $ret;
        }
        
        foreach ($items as $i) {
            $ret[$this -> getKey($i)] = $this -> getDesc($i);
        }
        
        return $ret;
    }
}

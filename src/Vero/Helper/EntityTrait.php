<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Helper;

use Traversable;

/**
 * Common methods for Entities stored in Doctrine ORM.
 */
trait EntityTrait
{
    /**
     * Fill entity with data, if posible.
     * 
     * @param Traversable|array|null $data
     * @return self
     */
    public function fill($data)
    {
        if ($data === null) {
            return $this;
        }
        
        if (!is_array($data) && !$data instanceof Traversable) {
            throw new \DomainException('Data to fill Entity must be array or Traversable!');
        }
        
        foreach ($data as $key => $value) {
            $setter = 'set'.$key;
            
            if (method_exists($this, $setter)) {
                $this -> $setter($value);
            }
        }
        
        return $this;
    }
}
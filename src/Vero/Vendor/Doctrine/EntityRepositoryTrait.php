<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Doctrine;

/**
 * Custom repository methods.
 */
trait EntityRepositoryTrait
{
    /**
     * Find entity by ID or throw exception.
     * 
     * @param mixed
     * @return array
     * @throws Exception
     */
    public function get($id)
    {
        $item = $this -> find($id);
        
        if (!$item) {
            throw new Exception();
        }
        
        return $item;
    }
    
    /**
     * Find entities by IDs or throw exception.
     * Works only for one-column IDs.
     * 
     * @param array|int|string
     * @return array
     * @throws Exception
     */
    public function getArray($ids)
    {
        $id = $this -> getClassMetadata() -> getIdentifier();
        
        $items = $this -> createQueryBuilder('e')
            -> where('e.'.$id[0].' IN (:ids)')
            -> setParameter('ids', (array) $ids)
            -> getQuery()
            -> execute();
        
        if (!$items) {
            throw new Exception();
        }
        
        return $items;
    }
    
    /**
     * Check if specified key is 
     * not empty scalar value or 
     * not empty array or 
     * not array with empty values only.
     * 
     * @param array|\ArrayAccess
     * @param string
     * @return boolean
     */
    protected static function notEmpty($array, $key)
    {
        if (empty($array[$key])) {
            return false;
        }
        
        if (is_array($array[$key])) {
            return (boolean) array_filter($array[$key], 'strlen');
        }
        
        return true;
    }
}

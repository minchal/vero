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
}

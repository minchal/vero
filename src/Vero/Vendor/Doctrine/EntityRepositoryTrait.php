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
     */
    public function get($id)
    {
        $item = $this -> find($id);
        
        if (!$item) {
            throw new Exception();
        }
        
        return $item;
    }
}

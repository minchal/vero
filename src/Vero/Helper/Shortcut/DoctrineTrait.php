<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Helper\Shortcut;

/**
 * Shortcuts to use doctrine's EntityManagers.
 * 
 * Requires: DITrait
 */
trait DoctrineTrait
{
    /**
     * Get Doctrine Entity Manager.
     * 
     * @return \Vero\Vendor\Doctrine\EntityManager
     */
    public function em()
    {
        return $this -> get('doctrine');
    }
    
    /**
     * Get repositiory from Doctrine Entity Manager.
     * PHPDoc here for default configuration.
     * 
     * @param string
     * @return \Doctrine\ORM\EntityRepository
     */
    public function repository($repo = null)
    {
        return $this -> get('repository', $repo);
    }
    
    /**
     * Persist single entity in EntityManager and flush.
     * 
     * @param object
     * @return self
     */
    public function persist($entity)
    {
        $this -> em()
            -> persist($entity)
            -> flush();
        
        return $this;
    }
    
    /**
     * Remove single entity from EntityManager and flush.
     * 
     * @param object
     * @return self
     */
    public function remove($entity)
    {
        $this -> em()
            -> remove($entity)
            -> flush();
        
        return $this;
    }
}

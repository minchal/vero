<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\ACL;

/**
 * ACL Savable Backend.
 * Extends backend of writing capability.
 */
interface WritableBackend extends Backend
{
    /**
     * Save ACL data for speciefied role.
     * 
     * @param string
     */
    public function save($role, array $data);
}

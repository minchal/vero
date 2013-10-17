<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\ACL;

/**
 * Role interface.
 */
interface Role
{
    /**
     * Get name of the role.
     * 
     * @return string
     */
    public function getRole();
}

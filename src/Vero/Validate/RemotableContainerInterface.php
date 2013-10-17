<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate;

/**
 * Container with fields checked from remote request.
 */
interface RemotableContainerInterface
{
    /**
     * Check, if field from container can be checked from remote (AJAX) request.
     * 
     * @param string
     * @return boolean
     */
    public function canRemoteCheck($field);
}

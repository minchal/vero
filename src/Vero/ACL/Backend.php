<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\ACL;

/**
 * ACL Backend.
 * Provides data for each role.
 */
interface Backend
{
    /**
     * Get keys array for specific role.
     * 
     * Example:
     *   admin =>
     *      access => true
     *      items =>
     *         news => false
     *         * => true
     *   user  =>
     *      access => true
     * 
     * @param string
     * @return array
     */
    public function get($role);
}

<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Routing;

/**
 * Route interface.
 * Route schould allow to:
 *  - match URL (and later get releated action)
 *  - build URL part (with optional params)
 */
interface Route
{
    /**
     * Get prefix (constant part) of this route.
     * Example:
     *  /news/:id/delete => /news/
     * 
     * @return string
     */
    public function getPrefix();
    
    /**
     * Get action releated with this route.
     * 
     * @return string 
     */
    public function getAction();
    
    /**
     * Get params names available to use in url() method.
     * 
     * @return array Number indexed and ordered list: (0=>name1, name2, ...)
     */
    public function getAvailableParams();
    
    /**
     * Get URL with filled parameters.
     * 
     * @params array $params String indexed array of params
     * @return string
     */
    public function url($params = array());
    
    /**
     * Try to match URL to pattern of this route.
     * 
     * @param string
     * @param array Reference to array, that will be filled with founded and matched arguments
     * @return boolean True if $url is matched
     */
    public function match($url, &$args = array());
    
    /**
     * Return Route as string for debugging purposes.
     * 
     * @return string
     */
    public function __toString();
}

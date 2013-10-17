<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Routing;

/**
 * Action Part for URL, that can be served as string when it's needed.
 */
interface ActionPart
{
    /**
     * Return action as string.
     * 
     * @return string
     */
    public function __toString();
    
    /**
     * Replace parameter.
     * 
     * @param string
     * @param string
     */
    public function replace($from, $to);
}

<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Helper\Shortcut;

/**
 * Shortcuts to use default router.
 * 
 * Requires: DITrait
 */
trait RouterTrait
{
    /**
     * Shortcut to call url() method on Router object.
     * 
     * @see \Vero\Routing\Router::url()
     * @return Vero\Routing\URL
     */
    public function url()
    {
        return call_user_func_array([$this -> get('router'), 'url'], func_get_args());
    }
}

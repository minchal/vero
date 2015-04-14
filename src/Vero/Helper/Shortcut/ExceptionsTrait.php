<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Helper\Shortcut;

use Vero\Web\Exception;
use Vero\Application\TranslatableException as AppException;

/**
 * Shortcuts to use default translator service.
 * 
 * Requires: DITrait
 */
trait ExceptionsTrait
{
    /**
     * @return Exception\NotFound
     */
    public function notFound()
    {
        return Exception\NotFound::url($this -> get('request') -> url());
    }
    
    /**
     * @return Exception\AccessDenied
     */
    public function accessDenied($resource = null)
    {
        return Exception\AccessDenied::resource($resource);
    }
    
    /**
     * @return AppException
     */
    public function exception($msg, $section = null, $params = [])
    {
        return new AppException($msg, $section, $params);
    }
}

<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Helper\Shortcut;

use Vero\Web\Response;
use Vero\View\Json;

/**
 * Shortcuts for creating Response objects.
 * 
 * Requires: DITrait
 */
trait ResponseTrait
{
    /**
     * Get instance of Response.
     * 
     * @return Response
     */
    public function response($body = null)
    {
        return new Response($body);
    }
    
    /**
     * Get instance of Response with redirection.
     * If no URL is specified, current Request URL is choosen.
     * 
     * @param \Vero\Router\URL|null
     */
    public function redirect($url = null)
    {
        return $this -> response() -> redirect($url===null ? $this->get('request')->url() : $url);
    }
    
    /**
     * Get instance of default templated view.
     * PHPDoc here for default configuration.
     * 
     * In typical templating service implementation 
     * tpl file name can be guessed from Route ID.
     * 
     * @param string $tpl
     * @param array $data
     * @return \Vero\View\Twig
     */
    public function render()
    {
        return $this -> get('templating', func_get_args());
    }
    
    /**
     * Get Json response object.
     * 
     * @param mixed
     * @return Json
     */
    public function json($data)
    {
        return (new Json($data))
            -> jsonp($this -> get('request') -> get('callback'));
    }
}

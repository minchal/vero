<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Twig;

use Vero\Routing\Router;
use Vero\Routing\URL;

/**
 * This extension adds functions to create URLs and 
 * prefixing assets (javascripts, stylesheets, images) in templates.
 * 
 * @see Router::url()
 */
class RouterExtension extends \Twig_Extension
{
    /**
     * @var Router
     */
    private $router;
    
    /**
     * @var string
     */
    private $assets;
    
    /**
     * @param Router
     * @param string|URL Prefix to all assets (e.g. /vero/public/)
     */
    public function __construct(Router $router, $assets = '')
    {
        $this -> router = $router;
        $this -> assets = $assets;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'router';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            'url'   => new \Twig_SimpleFunction('url', [$this, 'url']),
            'asset' => new \Twig_SimpleFunction('asset', [$this, 'asset']),
        ];
    }
    
    /**
     * @see Router::url()
     * @return Vero\Routing\URL
     */
    public function url($id = null)
    {
        try {
            return call_user_func_array([$this->router, 'url'], func_get_args());
        } catch (\OutOfRangeException $e) {
        }
        
        return $id;
    }
    
    /**
     * @param string
     * @return string
     */
    public function asset($url)
    {
        return $this -> assets . $url;
    }
}

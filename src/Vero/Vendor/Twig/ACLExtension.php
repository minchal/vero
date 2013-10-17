<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Twig;

use Vero\DependencyInjection\Container;

/**
 * This extension works with DI Container, to load ACL service 
 * (and session role - if posible), only when it's used.
 */
class ACLExtension extends \Twig_Extension
{
    /**
     * @var Container
     */
    protected $container;
    
    /**
     * @var string
     */
    protected $service;
    
    /**
     * @var \Vero\ACL\ACL
     */
    protected $acl;
    
    /**
     * Optionaly, specify service name in DI Container.
     * 
     * @param Container
     * @param string
     */
    public function __construct(Container $container, $service = 'acl')
    {
        $this -> container = $container;
        $this -> service = $service;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'acl';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'acl' => new \Twig_SimpleFunction('acl', [$this, 'check']),
        );
    }
    
    /**
     * @see \Vero\ACL\ACL::check()
     * @return boolean
     */
    public function check($key, $role = null)
    {
        if (!$this -> acl) {
            $this -> acl = $this -> container -> get($this -> service);
        }
        
        return $this -> acl -> check($key, $role);
    }
}

<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Twig;

use Vero\I18n\Translator;

/**
 * Extension adds support for internalization in templates.
 */
class I18nExtension extends \Twig_Extension
{
    /**
     * @var Translator
     */
    protected $i18n;
    
    /**
     * 
     */
    public function __construct(Translator $i18n)
    {
        $this -> i18n = $i18n;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'i18n';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return array(
            'domain' => new I18n\DomainTokenParser(),
        );
    }
    
    /**
     * This global is used in {% domain %} and {% enddomain %} tags.
     * 
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return array(
            'i18n' => $this -> i18n,
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'i18n' => new \Twig_SimpleFilter('i18n', [$this -> i18n, 'get']),
            'i18ng' => new \Twig_SimpleFilter('i18ng', [$this -> i18n, 'getGlobal']),
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'i18n' => new \Twig_SimpleFunction('i18n', [$this -> i18n, 'get']),
            'i18ng' => new \Twig_SimpleFunction('i18ng', [$this -> i18n, 'getGlobal']),
        );
    }
}

<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Twig;

use Vero\I18n\Translator;
use Vero\I18n\TextDateHelper;

/**
 * Extension adds support for internalization in templates.
 */
class I18nExtension extends \Twig_Extension
{
    /** @var Translator */
    protected $i18n;
    
    /** @var TextDateHelper */
    protected $textDateHelper;
    
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
        return [
            'domain' => new I18n\DomainTokenParser(),
        ];
    }
    
    /**
     * This global is used in {% domain %} and {% enddomain %} tags.
     * 
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return [
            'i18n' => $this -> i18n,
            'format' => $this -> i18n -> getFormatter(),
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            'i18n' => new \Twig_SimpleFilter('i18n', [$this -> i18n, 'get']),
            'i18ng' => new \Twig_SimpleFilter('i18ng', [$this -> i18n, 'getGlobal']),
            'textDate' => new \Twig_SimpleFilter('textDate', [$this, 'textDate']),
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            'i18n' => new \Twig_SimpleFunction('i18n', [$this -> i18n, 'get']),
            'i18ng' => new \Twig_SimpleFunction('i18ng', [$this -> i18n, 'getGlobal']),
            'textDate' => new \Twig_SimpleFilter('textDate', [$this, 'textDate']),
        ];
    }
    
    public function textDate($date)
    {
        if (!$this -> textDateHelper instanceof TextDateHelper) {
            $this -> textDateHelper = new TextDateHelper($this -> i18n);
        }
        
        return $this -> textDateHelper -> format($date);
    }
}

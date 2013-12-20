<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\View;

use Vero\Web\Response;
use Vero\Web\ResponseBody;
use Vero\DependencyInjection as DI;

/**
 * View using Twig library to output HTML.
 */
class Twig implements ResponseBody, DI\Dependent
{
    protected $tpl;
    
    protected $container;
    
    protected $widgets;
    protected $data;
    
    protected $compiled;
    protected $signatureKey;
    
    protected $format = 'text/html';
    
    /**
     * Create view with speciefied template file name.
     * 
     * @param string|null
     * @param array Data for template
     */
    public function __construct($tpl = null, array $data = [])
    {
        $this -> tpl  = $tpl;
        $this -> data = $data;
    }
    
    /**
     * Real constructor, initializes Twig library.
     * 
     * Container should have factory for Twig library.
     * 
     * @return self
     */
    public function setContainer(DI\Container $container)
    {
        $this -> container = $container;
        
        if (!$this -> widgets instanceof Widget\Manager) {
            $this -> widgets = new Widget\Manager($container);
        }
        return $this;
    }
    
    /**
     * Set template file.
     * 
     * @return self
     */
    public function setTemplate($tpl)
    {
        $this -> tpl = $tpl;
        return $this;
    }
    
    /**
     * Set Content-Type header send by this view.
     * 
     * @param string
     * @return self
     */
    public function setFormat($type)
    {
        $this -> format = $type;
        return $this;
    }
    
    /**
     * Set Content-Type header for XML response.
     * 
     * @param string
     * @return self
     */
    public function setXmlFormat()
    {
        return $this -> setFormat('application/xml');
    }
    
    /**
     * Set Content-Type header for ATOM response.
     * 
     * @param string
     * @return self
     */
    public function setAtomFormat()
    {
        return $this -> setFormat('application/atom+xml');
    }
    
    /**
     * Set Widget Manager instance.
     */
    public function setWidgetsManager(Widget\Manager $widgets)
    {
        $this -> widgets = $widgets;
    }
    
    /**
     * @return \Vero\View\Widget\Manager
     */
    public function getWidgetsManager()
    {
        return $this -> widgets;
    }
    
    /**
     * @see set()
     */
    public function __set($name, $value)
    {
        return $this -> set($name, $value);
    }
    
    /**
     * Add variable to template.
     * 
     * @param string
     * @param mixed
     * @return self
     */
    public function set($name, $value)
    {
        $this -> data[$name] = $value;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function prepare(Response $response, $buffer = null)
    {
        $response -> header('Content-Type', $this->format . '; charset=UTF-8');
        
        $this -> set('signature', $this -> getSignatureKey());
        $this -> set('debug', $buffer);
        
        $this -> compiled = $this -> render();
    }
    
    /**
     * Render View.
     * 
     * @return string
     */
    public function render()
    {
        try {
            $twig = $this -> container -> get('twig');
            $twig -> addGlobal('widgets', $this -> widgets);
            
            return $twig -> render($this -> tpl, $this -> data);
        } catch (\Twig_Error_Runtime $e) {
            if ($p = $e -> getPrevious()) {
                throw $p;
            }
            
            throw $e;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function send()
    {
        echo str_replace($this->getSignatureKey(), $this->container->get('app')->signature(), $this->compiled);
    }
    
    /**
     * Return random string to replace with App signature.
     * 
     * @return string
     */
    protected function getSignatureKey()
    {
        if (!$this -> signatureKey) {
            $this -> signatureKey = uniqid('Vero_View_Twig::SIGNATURE_');
        }
        
        return $this -> signatureKey;
    }
}
